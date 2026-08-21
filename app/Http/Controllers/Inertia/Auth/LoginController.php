<?php

namespace App\Http\Controllers\Inertia\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function showLoginForm(): Response
    {
        return Inertia::render('Auth/Login');
    }

    protected function redirectTo(): string
    {
        return route('inertia.dashboard');
    }

    protected function authenticated(Request $request, mixed $user): RedirectResponse
    {
        $request->session()->forget('url.intended');

        return redirect()->route('inertia.dashboard');
    }

    protected function loggedOut(Request $request): RedirectResponse
    {
        return redirect()->route('inertia.login');
    }
}
