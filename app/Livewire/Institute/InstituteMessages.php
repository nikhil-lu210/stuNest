<?php

namespace App\Livewire\Institute;

use App\Models\Application;
use App\Models\Institute;
use App\Models\InstituteRepresentative;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InstituteMessages extends Component
{
    public string $messageBody = '';

    /** @var 'list'|'chat' */
    public string $mobilePanel = 'list';

    public ?string $activeThreadType = null;

    public ?int $activeThreadId = null;

    public int $messageInputKey = 0;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Institute Representative'), 403);

        $institute = $this->resolveInstitute();

        $rawApplicationId = request()->query('application_id');
        if ($rawApplicationId !== null && $rawApplicationId !== '' && (int) $rawApplicationId > 0) {
            $this->selectConversation((int) $rawApplicationId, 'application');

            return;
        }

        $rawStudentId = request()->query('student_id');
        if ($rawStudentId !== null && $rawStudentId !== '' && (int) $rawStudentId > 0) {
            $this->selectConversation((int) $rawStudentId, 'support');

            return;
        }

        $rawUserId = request()->query('user_id');
        if ($rawUserId !== null && $rawUserId !== '' && (int) $rawUserId > 0) {
            $target = User::query()->whereKey((int) $rawUserId)->whereRoleName('Student')->first();
            if ($target !== null && $this->studentBelongsToInstitute($target, $institute)) {
                $this->selectConversation($target->id, 'support');
            }
        }
    }

    public function selectConversation(int $threadId, string $type): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Institute Representative'), 403);
        abort_unless(in_array($type, ['application', 'support'], true), 403);

        $institute = $this->resolveInstitute();

        if ($type === 'application') {
            $application = Application::query()
                ->whereKey($threadId)
                ->with(['property', 'student'])
                ->first();
            abort_unless(
                $application !== null && $this->instituteRepCanAccessApplicationThread($user, $institute, $application),
                403
            );
            $this->markIncomingReadForApplication($threadId, $user->id);
        } else {
            $student = User::query()->whereKey($threadId)->whereRoleName('Student')->first();
            abort_unless($student !== null && $this->studentBelongsToInstitute($student, $institute), 403);
            $this->markIncomingReadForSupport($institute->id, $threadId, $user->id);
        }

        $this->activeThreadType = $type;
        $this->activeThreadId = $threadId;
        $this->mobilePanel = 'chat';
    }

    public function backToList(): void
    {
        $this->mobilePanel = 'list';
        $this->activeThreadType = null;
        $this->activeThreadId = null;
    }

    public function sendMessage(): void
    {
        $this->validate([
            'messageBody' => ['required', 'string', 'max:5000'],
        ]);

        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Institute Representative'), 403);

        $institute = $this->resolveInstitute();

        if ($this->activeThreadType === 'application' && $this->activeThreadId) {
            $application = Application::query()
                ->whereKey($this->activeThreadId)
                ->with(['property', 'student'])
                ->first();

            abort_unless(
                $application !== null && $this->instituteRepCanAccessApplicationThread($user, $institute, $application),
                403
            );

            Message::create([
                'application_id' => $application->id,
                'support_institute_id' => null,
                'support_student_id' => null,
                'sender_id' => $user->id,
                'body' => trim($this->messageBody),
                'is_read' => false,
            ]);
        } elseif ($this->activeThreadType === 'support' && $this->activeThreadId) {
            $student = User::query()->whereKey($this->activeThreadId)->whereRoleName('Student')->first();
            abort_unless($student !== null && $this->studentBelongsToInstitute($student, $institute), 403);

            Message::create([
                'application_id' => null,
                'support_institute_id' => $institute->id,
                'support_student_id' => $student->id,
                'sender_id' => $user->id,
                'body' => trim($this->messageBody),
                'is_read' => false,
            ]);
        } else {
            abort(403);
        }

        $this->reset('messageBody');
        $this->messageInputKey++;

        $this->js(<<<'JS'
            const scrollInstituteChatToBottom = () => {
                const el = document.getElementById('institute-chat-scroll');
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            };
            const focusInstituteMessageInput = () => {
                const input = document.getElementById('institute-message-input');
                if (input && typeof input.focus === 'function') {
                    input.focus({ preventScroll: true });
                }
            };
            scrollInstituteChatToBottom();
            focusInstituteMessageInput();
            requestAnimationFrame(() => {
                scrollInstituteChatToBottom();
                focusInstituteMessageInput();
                requestAnimationFrame(() => {
                    scrollInstituteChatToBottom();
                    focusInstituteMessageInput();
                });
            });
            setTimeout(() => {
                scrollInstituteChatToBottom();
                focusInstituteMessageInput();
            }, 50);
            setTimeout(() => {
                scrollInstituteChatToBottom();
                focusInstituteMessageInput();
            }, 200);
        JS);
    }

    protected function markIncomingReadForApplication(int $applicationId, int $readerUserId): void
    {
        Message::query()
            ->where('application_id', $applicationId)
            ->where('sender_id', '!=', $readerUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    protected function markIncomingReadForSupport(int $instituteId, int $studentId, int $readerUserId): void
    {
        Message::query()
            ->whereNull('application_id')
            ->where('support_institute_id', $instituteId)
            ->where('support_student_id', $studentId)
            ->where('sender_id', '!=', $readerUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * @return Collection<int, array{type: string, id: int, student: ?User, property_title: ?string, preview: ?string, last_message_at: Carbon, unread: int, application: ?Application}>
     */
    protected function buildConversations(User $user, Institute $institute): Collection
    {
        $applicationRows = Application::query()
            ->where(function (Builder $q) use ($user, $institute) {
                $q->whereHas('property', fn ($pq) => $pq->where('user_id', $user->id))
                    ->orWhereHas('student', function (Builder $sq) use ($institute) {
                        $sq->whereRoleName('Student')
                            ->where(function (Builder $sqq) use ($institute) {
                                $sqq->where('institution_id', $institute->id)
                                    ->orWhereHas('instituteLocation', fn (Builder $lq) => $lq->where('institute_id', $institute->id));
                            });
                    });
            })
            ->whereHas('messages')
            ->with(['property.area', 'property.city', 'student', 'latestMessage'])
            ->withMax('messages', 'created_at')
            ->withCount([
                'messages as unread_from_counterparty' => function ($q) use ($user) {
                    $q->where('sender_id', '!=', $user->id)->where('is_read', false);
                },
            ])
            ->get()
            ->map(function (Application $app) {
                $lastAt = $app->messages_max_created_at
                    ? Carbon::parse($app->messages_max_created_at)
                    : $app->updated_at;

                return [
                    'type' => 'application',
                    'id' => $app->id,
                    'student' => $app->student,
                    'property_title' => $app->property?->display_title ?? __('Listing'),
                    'preview' => $app->latestMessage?->body,
                    'last_message_at' => $lastAt,
                    'unread' => (int) $app->unread_from_counterparty,
                    'application' => $app,
                ];
            });

        $supportStudentIds = Message::query()
            ->whereNull('application_id')
            ->where('support_institute_id', $institute->id)
            ->whereNotNull('support_student_id')
            ->distinct()
            ->pluck('support_student_id');

        $supportRows = collect();
        foreach ($supportStudentIds as $studentId) {
            $student = User::query()->whereKey($studentId)->whereRoleName('Student')->first();
            if ($student === null || ! $this->studentBelongsToInstitute($student, $institute)) {
                continue;
            }

            $lastAt = Message::query()
                ->whereNull('application_id')
                ->where('support_institute_id', $institute->id)
                ->where('support_student_id', $studentId)
                ->max('created_at');

            if ($lastAt === null) {
                continue;
            }

            $preview = Message::query()
                ->whereNull('application_id')
                ->where('support_institute_id', $institute->id)
                ->where('support_student_id', $studentId)
                ->latest('id')
                ->value('body');

            $unread = Message::query()
                ->whereNull('application_id')
                ->where('support_institute_id', $institute->id)
                ->where('support_student_id', $studentId)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->count();

            $supportRows->push([
                'type' => 'support',
                'id' => (int) $studentId,
                'student' => $student,
                'property_title' => null,
                'preview' => $preview,
                'last_message_at' => Carbon::parse($lastAt),
                'unread' => (int) $unread,
                'application' => null,
            ]);
        }

        return $applicationRows->concat($supportRows)->sortByDesc('last_message_at')->values();
    }

    private function resolveInstitute(): Institute
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $representation = InstituteRepresentative::query()
            ->where('user_id', $user->id)
            ->with('institute')
            ->first();

        abort_if($representation === null || ! $representation->institute instanceof Institute, 403);

        return $representation->institute;
    }

    private function studentBelongsToInstitute(User $student, Institute $institute): bool
    {
        if ((int) $student->institution_id === (int) $institute->id) {
            return true;
        }

        $student->loadMissing('instituteLocation');

        return $student->instituteLocation !== null
            && (int) $student->instituteLocation->institute_id === (int) $institute->id;
    }

    private function instituteRepCanAccessApplicationThread(User $rep, Institute $institute, Application $application): bool
    {
        $application->loadMissing(['property', 'student']);

        $property = $application->property;
        if ($property !== null && (int) $property->user_id === (int) $rep->id) {
            return true;
        }

        $student = $application->student;
        if ($student === null) {
            return false;
        }

        return $this->studentBelongsToInstitute($student, $institute);
    }

    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Institute Representative'), 403);

        $institute = $this->resolveInstitute();

        $conversations = $this->buildConversations($user, $institute);

        $activeApplication = null;
        $activeSupportStudent = null;
        /** @var Collection<int, Message> $activeMessages */
        $activeMessages = collect();

        if ($this->activeThreadType === 'application' && $this->activeThreadId) {
            $activeApplication = Application::query()
                ->whereKey($this->activeThreadId)
                ->with([
                    'property.area',
                    'property.city',
                    'student',
                    'messages' => fn ($q) => $q->orderBy('created_at'),
                ])
                ->first();

            if (
                ! $activeApplication
                || ! $this->instituteRepCanAccessApplicationThread($user, $institute, $activeApplication)
            ) {
                $this->activeThreadType = null;
                $this->activeThreadId = null;
                $this->mobilePanel = 'list';
            } else {
                $activeMessages = $activeApplication->messages;
            }
        } elseif ($this->activeThreadType === 'support' && $this->activeThreadId) {
            $activeSupportStudent = User::query()->whereKey($this->activeThreadId)->whereRoleName('Student')->first();
            if (
                $activeSupportStudent === null
                || ! $this->studentBelongsToInstitute($activeSupportStudent, $institute)
            ) {
                $this->activeThreadType = null;
                $this->activeThreadId = null;
                $this->mobilePanel = 'list';
            } else {
                $activeMessages = Message::query()
                    ->whereNull('application_id')
                    ->where('support_institute_id', $institute->id)
                    ->where('support_student_id', $this->activeThreadId)
                    ->orderBy('created_at')
                    ->get();
            }
        }

        return view('livewire.institute.institute-messages', [
            'conversations' => $conversations,
            'institute' => $institute,
            'activeApplication' => $activeApplication,
            'activeSupportStudent' => $activeSupportStudent,
            'activeMessages' => $activeMessages,
        ])->layout('layouts.institute', [
            'title' => __('Messages'),
            'pageTitle' => __('Messages'),
            'pageSubtitle' => $institute->name,
            'instituteOrgName' => $institute->name,
        ]);
    }
}
