<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Wishlist / saved property ids for the current session (DB pivot or session fallback).
 */
final class SavedPropertyIds
{
    /**
     * @return array<int, int>
     */
    public static function forRequest(Request $request): array
    {
        if (! $request->user()) {
            return [];
        }

        if (Schema::hasTable('saved_properties')) {
            return DB::table('saved_properties')
                ->where('user_id', $request->user()->id)
                ->pluck('property_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        $ids = session()->get('explore_saved_property_ids', []);
        if (! is_array($ids)) {
            return [];
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }
}
