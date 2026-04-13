<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vuexy administration is limited to users with at least one Spatie role on the `web` guard
 * (e.g. Developer, Super Admin). Client marketplace users (student, landlord, agent, institute)
 * use other guards and are redirected to their portal or home.
 */
class EnsureUserCanAccessAdministration
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->hasAdministrationAccess()) {
            return $next($request);
        }

        return redirect()
            ->to($user?->clientPortalHomeUrl() ?? route('client.home'))
            ->with('warning', __('You do not have access to the administration area.'));
    }
}
