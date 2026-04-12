<?php

namespace App\Http\Controllers\Administration\Application;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Application::class);

        $applications = Application::query()
            ->with(['property', 'student'])
            ->where('status', Application::STATUS_ACCEPTED)
            ->whereNotNull('accepted_at')
            ->latest('accepted_at')
            ->paginate(20)
            ->withQueryString();

        return view('administration.application.tenancies', compact('applications'));
    }
}
