<?php

namespace App\Http\Controllers\Inertia\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Inertia\Inertia;
use Inertia\Response;

class ConfirmPasswordController extends Controller
{
    use ConfirmsPasswords;

    public function showConfirmForm(): Response
    {
        return Inertia::render('Auth/ConfirmPassword');
    }

    protected function redirectTo(): string
    {
        return route('inertia.dashboard');
    }
}
