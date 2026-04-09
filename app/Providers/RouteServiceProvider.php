<?php

namespace App\Providers;

/**
 * Holds shared route constants (e.g. post-login redirect). Auth scaffolding references this class.
 */
class RouteServiceProvider
{
    /** Default post-auth fallback (staff); use role-based redirects for marketplace users. */
    public const HOME = '/administration/dashboard';
}
