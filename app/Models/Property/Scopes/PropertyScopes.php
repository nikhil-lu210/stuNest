<?php

namespace App\Models\Property\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait PropertyScopes
{
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', static::STATUS_DRAFT);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', static::STATUS_PENDING);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', static::STATUS_PUBLISHED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', static::STATUS_REJECTED);
    }

    public function scopeLetAgreed(Builder $query): Builder
    {
        return $query->where('status', static::STATUS_LET_AGREED);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', static::STATUS_ARCHIVED);
    }
}
