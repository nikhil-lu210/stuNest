<?php

namespace App\Livewire\Student;

use App\Models\Application;
use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentMessages extends Component
{
    public ?int $activeApplicationId = null;

    public string $messageBody = '';

    /** @var 'list'|'chat' */
    public string $mobilePanel = 'list';

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasStudentRole(), 403);

        $applications = $this->applicationsQuery()->get();
        if ($applications->isNotEmpty()) {
            $this->activeApplicationId = $applications->first()->id;
            $this->markIncomingReadFor($this->activeApplicationId);
        }
    }

    public function selectConversation(int $applicationId): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasStudentRole(), 403);

        $application = Application::query()
            ->where('user_id', $user->id)
            ->whereKey($applicationId)
            ->first();

        abort_unless($application, 403);

        $this->activeApplicationId = $application->id;
        $this->mobilePanel = 'chat';

        $this->markIncomingReadFor($applicationId);
    }

    public function backToList(): void
    {
        $this->mobilePanel = 'list';
    }

    public function sendMessage(): void
    {
        $this->validate([
            'messageBody' => ['required', 'string', 'max:5000'],
        ]);

        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasStudentRole(), 403);

        $application = Application::query()
            ->where('user_id', $user->id)
            ->whereKey($this->activeApplicationId)
            ->first();

        abort_unless($application, 403);

        Message::create([
            'application_id' => $application->id,
            'sender_id' => $user->id,
            'body' => trim($this->messageBody),
            'is_read' => false,
        ]);

        $this->messageBody = '';

        $this->js(<<<'JS'
            const el = document.getElementById('student-chat-scroll');
            if (el) el.scrollTop = el.scrollHeight;
        JS);
    }

    protected function markIncomingReadFor(int $applicationId): void
    {
        $userId = Auth::id();
        if ($userId === null) {
            return;
        }

        Message::query()
            ->where('application_id', $applicationId)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    protected function applicationsQuery(): Builder
    {
        $userId = Auth::id();

        return Application::query()
            ->where('user_id', $userId)
            ->with(['property.creator', 'latestMessage'])
            ->withCount([
                'messages as unread_from_landlord_count' => function ($q) use ($userId) {
                    $q->where('sender_id', '!=', $userId)->where('is_read', false);
                },
            ])
            ->where(function ($q) {
                $q->whereHas('messages')
                    ->orWhereIn('status', [
                        Application::STATUS_PENDING,
                        Application::STATUS_ACCEPTED,
                    ]);
            })
            ->latest('updated_at');
    }

    public function render(): View
    {
        $applications = $this->applicationsQuery()->get();

        $activeApplication = null;
        if ($this->activeApplicationId) {
            $activeApplication = Application::query()
                ->where('user_id', Auth::id())
                ->whereKey($this->activeApplicationId)
                ->with(['property.creator', 'messages' => fn ($q) => $q->orderBy('created_at')])
                ->first();

            if (! $activeApplication && $applications->isNotEmpty()) {
                $this->activeApplicationId = $applications->first()->id;
                $activeApplication = Application::query()
                    ->where('user_id', Auth::id())
                    ->whereKey($this->activeApplicationId)
                    ->with(['property.creator', 'messages' => fn ($q) => $q->orderBy('created_at')])
                    ->first();
            }
        }

        return view('livewire.student.student-messages', [
            'applications' => $applications,
            'activeApplication' => $activeApplication,
        ]);
    }
}
