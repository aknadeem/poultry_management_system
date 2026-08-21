<?php

namespace App\Http\Controllers\Inertia\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Inertia\Inertia;
use Inertia\Response;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function showLinkRequestForm(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }
}
