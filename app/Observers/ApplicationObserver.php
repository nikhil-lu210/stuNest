<?php

namespace App\Observers;

use App\Models\Application;
use App\Models\Property\Property;
use Illuminate\Support\Facades\DB;

class ApplicationObserver
{
    public function created(Application $application): void
    {
        if ($application->status !== Application::STATUS_ACCEPTED) {
            return;
        }

        $this->processAcceptance($application);
    }

    public function updated(Application $application): void
    {
        if (! $application->wasChanged('status')) {
            return;
        }

        if ($application->status !== Application::STATUS_ACCEPTED) {
            return;
        }

        if ($application->getOriginal('status') === Application::STATUS_ACCEPTED) {
            return;
        }

        $this->processAcceptance($application);
    }

    private function processAcceptance(Application $application): void
    {
        if ($application->accepted_at !== null) {
            return;
        }

        DB::transaction(function () use ($application) {
            $application->forceFill(['accepted_at' => now()])->saveQuietly();

            $property = Property::query()->lockForUpdate()->find($application->property_id);

            if (! $property) {
                return;
            }

            $remaining = max(0, (int) $property->available_beds - 1);
            $property->available_beds = $remaining;

            if ($remaining === 0) {
                $property->status = Property::STATUS_LET_AGREED;
            }

            $property->save();
        });
    }
}
