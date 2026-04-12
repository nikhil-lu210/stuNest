<?php

namespace App\Models\Property\Relations;

use App\Models\Application;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\User;

trait PropertyRelations
{
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
