<?php

namespace App\Livewire\Property;

use App\Models\Application;
use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ApplyStatusBlock extends Component
{
    public int $propertyId;

    public string $variant = 'sidebar';

    public string $loginApplyUrl = '';

    public ?int $rentAmount = null;

    public string $rentPeriodLabel = '';

    public string $listingMinContractLabel = '';

    public ?Application $existingApplication = null;

    public function mount(
        int $propertyId,
        string $variant = 'sidebar',
        string $loginApplyUrl = '',
        ?int $existingApplicationId = null,
        ?int $rentAmount = null,
        string $rentPeriodLabel = '',
        string $listingMinContractLabel = '',
    ): void {
        $this->propertyId = $propertyId;
        $this->variant = $variant;
        $this->loginApplyUrl = $loginApplyUrl;
        $this->rentAmount = $rentAmount;
        $this->rentPeriodLabel = $rentPeriodLabel;
        $this->listingMinContractLabel = $listingMinContractLabel;

        if ($existingApplicationId !== null) {
            $this->existingApplication = Application::query()->find($existingApplicationId);
        } else {
            $this->refreshApplication();
        }
    }

    #[On('application-submitted')]
    public function onApplicationSubmitted(): void
    {
        $this->refreshApplication();

        $this->js('if (window.lucide && typeof lucide.createIcons === "function") { lucide.createIcons(); }');
    }

    public function refreshApplication(): void
    {
        $user = Auth::user();
        if (! $user instanceof User || ! $user->hasStudentRole()) {
            $this->existingApplication = null;

            return;
        }

        $this->existingApplication = $user->applications()
            ->where('property_id', $this->propertyId)
            ->latest()
            ->first();
    }

    public function render(): View
    {
        $property = Property::query()->findOrFail($this->propertyId);

        return view('livewire.property.apply-status-block', [
            'property' => $property,
            'existing' => $this->existingApplication,
            'variant' => $this->variant,
        ]);
    }
}
