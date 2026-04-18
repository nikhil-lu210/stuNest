<?php

namespace App\Models\Property\Accessors;

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
}
