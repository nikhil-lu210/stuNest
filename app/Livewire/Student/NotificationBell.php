<?php

namespace App\Livewire\Student;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NotificationBell extends Component
{
    public function markAsRead(string $notificationId): void
    {
        $user = $this->student();

        $notification = $user->notifications()->where('id', $notificationId)->first();

        if ($notification && $notification->unread()) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        $this->student()->unreadNotifications->markAsRead();
    }

    #[Computed]
    public function unreadCount(): int
    {
        return $this->student()->unreadNotifications()->count();
    }

    #[Computed]
    public function recentUnreadNotifications()
    {
        return $this->student()
            ->unreadNotifications()
            ->latest()
            ->take(5)
            ->get();
    }

    protected function student(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Student'), 403);

        return $user;
    }

    public function render(): View
    {
        return view('livewire.student.notification-bell');
    }
}
