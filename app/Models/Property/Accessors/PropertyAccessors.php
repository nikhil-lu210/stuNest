<?php

namespace App\Models\Property\Accessors;

use Carbon\Carbon;
use Illuminate\Support\Str;

trait PropertyAccessors
{
    public function getDisplayTitleAttribute(): string
    {
        $label = Str::headline(str_replace('_', ' ', (string) $this->listing_category));

        if ($this->area) {
            return $label.' · '.$this->area->name;
        }

        if ($this->city) {
            return $label.' · '.$this->city->name;
        }

        return $label !== '' ? $label : 'Listing #'.$this->id;
    }

    /**
     * First line under title (distance to campus).
     */
    public function getMarketingUniLineAttribute(): string
    {
        if ($this->distance_university_km !== null && $this->distance_university_km !== '') {
            return number_format((float) $this->distance_university_km, 1).' '.__('km from campus');
        }

        return __('Near campus');
    }

    /**
     * Second line: neighbourhood / city.
     */
    public function getMarketingAreaLineAttribute(): string
    {
        $parts = array_filter([
            $this->area?->name,
            $this->city?->name,
        ]);

        return implode(', ', array_unique($parts)) ?: __('Location on request');
    }

    public function getFullAddressLineAttribute(): string
    {
        return $this->marketing_area_line;
    }

    /**
     * Weekly rent for public UI (stored amount is modelled as whole currency units per week in this app).
     */
    public function getWeeklyRentDisplayAttribute(): string
    {
        return (string) (int) $this->rent_amount;
    }

    /**
     * Placeholder star rating until reviews exist (stable per listing).
     */
    public function getPublicStarRatingAttribute(): string
    {
        $base = 4.5 + (($this->id % 6) * 0.08);

        return number_format(min(5.0, $base), 1);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        $url = $this->getFirstMediaUrl('property_gallery', 'thumb');

        return $url !== '' ? $url : null;
    }

    /**
     * Earliest move-in date for booking validation (defaults to today when unset).
     */
    public function availableFromForBooking(): Carbon
    {
        return $this->available_from
            ? Carbon::parse($this->available_from)->startOfDay()
            : now()->startOfDay();
    }

    /**
     * Minimum tenancy length in weeks for booking (falls back from min_contract_length when unset).
     */
    public function minContractWeeksForBooking(): int
    {
        if ($this->min_contract_weeks !== null) {
            return max(1, (int) $this->min_contract_weeks);
        }

        return match ($this->min_contract_length ?? 'flexible') {
            '1_month' => 4,
            '3_months' => 13,
            '6_months' => 26,
            '1_year' => 52,
            'flexible' => 1,
            default => 26,
        };
    }

    /**
     * Minimum tenancy length in days (derived from min_contract_length).
     */
    public function minContractDaysForBooking(): int
    {
        return match ($this->min_contract_length ?? 'flexible') {
            '1_month' => 30,
            '3_months' => 90,
            '6_months' => 180,
            '1_year' => 365,
            'flexible' => 1,
            default => 30,
        };
    }

    /**
     * Minimum tenancy length in whole months (derived from min_contract_length).
     */
    public function minContractMonthsForBooking(): int
    {
        return match ($this->min_contract_length ?? 'flexible') {
            '1_month' => 1,
            '3_months' => 3,
            '6_months' => 6,
            '1_year' => 12,
            'flexible' => 1,
            default => 3,
        };
    }

    /**
     * Human-readable minimum contract (matches listing copy).
     */
    public function minContractLengthLabel(): string
    {
        return match ($this->min_contract_length ?? 'flexible') {
            '1_month' => __('1 month'),
            '3_months' => __('3 months'),
            '6_months' => __('6 months'),
            '1_year' => __('1 year'),
            'flexible' => __('Flexible'),
            default => Str::headline(str_replace('_', ' ', (string) $this->min_contract_length)),
        };
    }
}
