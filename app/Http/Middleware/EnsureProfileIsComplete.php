<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Redirect students with incomplete profiles to the profile completion screen.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        if (! $user->hasStudentRole()) {
            return $next($request);
        }

        if ((bool) $user->is_profile_complete) {
            return $next($request);
        }

        if ($request->routeIs('student.profile.edit')) {
            return $next($request);
        }

        if ($request->routeIs('logout')) {
            return $next($request);
        }

        if ($request->is('livewire/*')) {
            return $next($request);
        }

        return redirect()->route('student.profile.edit');
    }
}
