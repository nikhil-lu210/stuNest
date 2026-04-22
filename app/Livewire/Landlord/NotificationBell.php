<?php

namespace App\Livewire\Landlord;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $menuOpen = false;

    public function toggleMenu(): void
    {
        $this->menuOpen = ! $this->menuOpen;
    }

    public function closeMenu(): void
    {
        $this->menuOpen = false;
    }

    public function markAsRead(string $notificationId): void
    {
        $user = $this->landlord();

        $notification = $user->notifications()->where('id', $notificationId)->first();

        if ($notification && $notification->unread()) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        $this->landlord()->unreadNotifications->markAsRead();
        $this->menuOpen = false;
    }

    #[Computed]
    public function unreadCount(): int
    {
        return $this->landlord()->unreadNotifications()->count();
    }

    #[Computed]
    public function recentUnreadNotifications()
    {
        return $this->landlord()
            ->unreadNotifications()
            ->latest()
            ->take(5)
            ->get();
    }

    protected function landlord(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Landlord'), 403);

        return $user;
    }

    public function render(): View
    {
        return view('livewire.landlord.notification-bell');
    }
}
