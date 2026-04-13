<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Staff (Spatie roles on `web` guard) go to Vuexy; marketplace users to their client portal.
     */
    protected function redirectTo(): string
    {
        $user = auth()->user();
        if (! $user) {
            return RouteServiceProvider::HOME;
        }

        if ($user->hasAdministrationAccess()) {
            return RouteServiceProvider::HOME;
        }

        return $user->clientPortalHomeUrl();
    }
}
