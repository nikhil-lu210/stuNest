<?php

namespace App\Livewire\Property;

use App\Models\Application;
use App\Models\Property\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ApplyToProperty extends Component
{
    public Property $property;

    public bool $isOpen = false;

    public ?string $proposed_move_in = null;

    public ?int $proposed_duration = null;

    public string $message_to_landlord = '';

    public function mount(Property $property): void
    {
        $this->property = $property;
        $this->resetFormDefaults();
    }

    #[On('openApplicationModal')]
    public function openPanel(): void
    {
        $user = Auth::user();
        if (! $user instanceof User || ! $user->hasStudentRole()) {
            return;
        }

        $this->property->refresh();
        $this->resetFormDefaults();
        $this->resetValidation();
        $this->isOpen = true;
    }

    public function closePanel(): void
    {
        $this->isOpen = false;
    }

    public function submit(): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);
        abort_unless($user->hasStudentRole(), 403);

        $this->property->refresh();

        abort_unless($this->property->status === Property::STATUS_PUBLISHED, 404);

        $from = $this->property->availableFromForBooking()->format('Y-m-d');
        $min = $this->minimumDurationForRentUnit();
        $allowed = array_column($this->durationOptions, 'value');

        $this->validate([
            'proposed_move_in' => ['required', 'date', 'after_or_equal:'.$from],
            'proposed_duration' => ['required', 'integer', 'min:'.$min, Rule::in($allowed)],
            'message_to_landlord' => ['required', 'string', 'max:1000'],
        ]);

        $duplicate = Application::query()
            ->where('property_id', $this->property->id)
            ->where('user_id', $user->id)
            ->whereIn('status', [Application::STATUS_PENDING, Application::STATUS_ACCEPTED])
            ->exists();

        if ($duplicate) {
            $this->addError('application', __('You already have an active application for this property.'));

            return;
        }

        $proposedDurationWeeks = $this->proposedDurationAsWeeks();

        Application::query()->create([
            'property_id' => $this->property->id,
            'user_id' => $user->id,
            'proposed_move_in' => Carbon::parse($this->proposed_move_in)->format('Y-m-d'),
            'proposed_duration_weeks' => $proposedDurationWeeks,
            'message_to_landlord' => $this->message_to_landlord,
            'status' => Application::STATUS_PENDING,
        ]);

        $this->dispatch('application-submitted');

        session()->flash('success', __('Your application has been submitted.'));

        $toast = json_encode([
            'toast' => true,
            'position' => 'top-end',
            'icon' => 'success',
            'title' => __('Your application has been submitted.'),
            'showConfirmButton' => false,
            'timer' => 4000,
            'timerProgressBar' => true,
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $this->js(sprintf(
            'if (typeof Swal !== "undefined") { Swal.fire(%s); }',
            $toast
        ));

        $this->isOpen = false;
        $this->reset(['message_to_landlord']);
        $this->resetFormDefaults();
        $this->resetValidation();
    }

    /**
     * Dropdown options: value + label (Str::plural for units).
     *
     * @return array<int, array{value: int, label: string}>
     */
    #[Computed]
    public function durationOptions(): array
    {
        $rd = $this->property->rent_duration ?? 'week';

        return match ($rd) {
            'day' => $this->buildDayDurationOptions(),
            'month' => $this->buildMonthDurationOptions(),
            default => $this->buildWeekDurationOptions(),
        };
    }

    private function minimumDurationForRentUnit(): int
    {
        return match ($this->property->rent_duration ?? 'week') {
            'day' => $this->property->minContractDaysForBooking(),
            'month' => $this->property->minContractMonthsForBooking(),
            default => $this->property->minContractWeeksForBooking(),
        };
    }

    /**
     * Persist as weeks (DB column) for consistent reporting.
     */
    private function proposedDurationAsWeeks(): int
    {
        $v = (int) $this->proposed_duration;

        return match ($this->property->rent_duration ?? 'week') {
            'day' => max(1, (int) ceil($v / 7)),
            'month' => max(1, (int) round($v * 52 / 12)),
            default => max(1, $v),
        };
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function buildDayDurationOptions(): array
    {
        $min = max(1, $this->property->minContractDaysForBooking());
        $end = min(730, max(30, $min));

        $values = range($min, $end);
        if (count($values) > 60) {
            $step = max(1, (int) ceil(count($values) / 55));
            $values = range($min, $end, $step);
        }

        return array_map(function (int $n) {
            return [
                'value' => $n,
                'label' => $this->durationOptionLabel($n, 'day'),
            ];
        }, $values);
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function buildWeekDurationOptions(): array
    {
        $min = $this->property->minContractWeeksForBooking();
        $candidates = array_unique(array_merge([$min], [4, 13, 26, 44, 51]));
        sort($candidates);
        $values = array_values(array_filter($candidates, fn (int $w) => $w >= $min));

        return array_map(function (int $n) {
            return [
                'value' => $n,
                'label' => $this->durationOptionLabel($n, 'week'),
            ];
        }, $values);
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function buildMonthDurationOptions(): array
    {
        $min = $this->property->minContractMonthsForBooking();
        $values = range($min, 12);

        return array_map(function (int $n) {
            return [
                'value' => $n,
                'label' => $this->durationOptionLabel($n, 'month'),
            ];
        }, $values);
    }

    private function durationOptionLabel(int $value, string $unit): string
    {
        $singular = match ($unit) {
            'day' => __('day'),
            'month' => __('month'),
            default => __('week'),
        };

        $unitPlural = Str::plural($singular, $value);

        return sprintf('%d %s', $value, Str::title($unitPlural));
    }

    private function resetFormDefaults(): void
    {
        $this->proposed_move_in = $this->property->availableFromForBooking()->format('Y-m-d');
        $opts = $this->durationOptions;
        $this->proposed_duration = $opts[0]['value'] ?? $this->minimumDurationForRentUnit();
    }

    public function render(): View
    {
        return view('livewire.property.apply-to-property');
    }
}
