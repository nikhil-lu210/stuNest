<?php

namespace App\Livewire\Landlord;

use App\Models\Application;
use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LandlordMessages extends Component
{
    public ?int $activeApplicationId = null;

    public string $messageBody = '';

    /** @var 'list'|'chat' */
    public string $mobilePanel = 'list';

    public int $messageInputKey = 0;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);
    }

    public function selectConversation(int $applicationId): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

        $application = Application::query()
            ->whereKey($applicationId)
            ->whereHas('property', fn ($q) => $q->where('user_id', $user->id))
            ->first();

        abort_unless($application, 403);

        $this->activeApplicationId = $application->id;
        $this->mobilePanel = 'chat';

        $this->markIncomingReadFor($applicationId);
    }

    public function backToList(): void
    {
        $this->mobilePanel = 'list';
        $this->activeApplicationId = null;
    }

    public function sendMessage(): void
    {
        $this->validate([
            'messageBody' => ['required', 'string', 'max:5000'],
        ]);

        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

        $application = Application::query()
            ->whereKey($this->activeApplicationId)
            ->whereHas('property', fn ($q) => $q->where('user_id', $user->id))
            ->first();

        abort_unless($application, 403);

        Message::create([
            'application_id' => $application->id,
            'sender_id' => $user->id,
            'body' => trim($this->messageBody),
            'is_read' => false,
        ]);

        $this->reset('messageBody');
        $this->messageInputKey++;

        $this->js(<<<'JS'
            const scrollLandlordChatToBottom = () => {
                const el = document.getElementById('landlord-chat-scroll');
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            };
            const focusLandlordMessageInput = () => {
                const input = document.getElementById('landlord-message-input');
                if (input && typeof input.focus === 'function') {
                    input.focus({ preventScroll: true });
                }
            };
            scrollLandlordChatToBottom();
            focusLandlordMessageInput();
            requestAnimationFrame(() => {
                scrollLandlordChatToBottom();
                focusLandlordMessageInput();
                requestAnimationFrame(() => {
                    scrollLandlordChatToBottom();
                    focusLandlordMessageInput();
                });
            });
            setTimeout(() => {
                scrollLandlordChatToBottom();
                focusLandlordMessageInput();
            }, 50);
            setTimeout(() => {
                scrollLandlordChatToBottom();
                focusLandlordMessageInput();
            }, 200);
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
            ->whereHas('property', fn ($q) => $q->where('user_id', $userId))
            ->whereHas('messages')
            ->with(['property.area', 'property.city', 'student', 'latestMessage'])
            ->withMax('messages', 'created_at')
            ->withCount([
                'messages as unread_from_student_count' => function ($q) use ($userId) {
                    $q->where('sender_id', '!=', $userId)->where('is_read', false);
                },
            ])
            ->orderByDesc('messages_max_created_at');
    }

    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

        /** @var Collection<int, Application> $applications */
        $applications = $this->applicationsQuery()->get();

        $activeApplication = null;
        if ($this->activeApplicationId) {
            $activeApplication = Application::query()
                ->whereKey($this->activeApplicationId)
                ->whereHas('property', fn ($q) => $q->where('user_id', $user->id))
                ->with([
                    'property.area',
                    'property.city',
                    'student',
                    'messages' => fn ($q) => $q->orderBy('created_at'),
                ])
                ->first();

            if (! $activeApplication) {
                $this->activeApplicationId = null;
                $this->mobilePanel = 'list';
            }
        }

        return view('livewire.landlord.landlord-messages', [
            'applications' => $applications,
            'activeApplication' => $activeApplication,
        ])->layout('layouts.landlord', [
            'title' => __('Messages'),
            'pageTitle' => __('Messages'),
        ]);
    }
}
