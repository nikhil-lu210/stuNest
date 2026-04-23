<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'application_id',
        'support_institute_id',
        'support_student_id',
        'sender_id',
        'body',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function supportInstitute(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'support_institute_id');
    }

    public function supportStudent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'support_student_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    protected static function booted(): void
    {
        static::created(function (Message $message) {
            $message->application?->touch();
        });
    }
}
