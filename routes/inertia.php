<?php

use App\Http\Controllers\Inertia\Auth\ConfirmPasswordController;
use App\Http\Controllers\Inertia\Auth\ForgotPasswordController;
use App\Http\Controllers\Inertia\Auth\LoginController;
use App\Http\Controllers\Inertia\Auth\RegisterController;
use App\Http\Controllers\Inertia\Auth\ResetPasswordController;
use App\Http\Controllers\Inertia\DashboardController;
use App\Http\Controllers\Inertia\UserManagement\UserController as InertiaUserController;
use App\Http\Controllers\Inertia\UserManagement\UserRoleController as InertiaUserRoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('app')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('inertia.login');
        Route::post('login', [LoginController::class, 'login']);

        Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('inertia.register');
        Route::post('register', [RegisterController::class, 'register']);

        Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('inertia.password.request');
        Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('inertia.password.email');

        Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('inertia.password.reset');
        Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('inertia.password.update');
    });

    Route::middleware('auth')->group(function (): void {
        Route::get('/', function () {
            return redirect()->route('inertia.dashboard');
        });
        Route::get('dashboard', DashboardController::class)->name('inertia.dashboard');
        Route::post('logout', [LoginController::class, 'logout'])->name('inertia.logout');

        Route::get('confirm-password', [ConfirmPasswordController::class, 'showConfirmForm'])->name('inertia.password.confirm');
        Route::post('confirm-password', [ConfirmPasswordController::class, 'confirm']);

        Route::prefix('usermanagement')->group(function (): void {
            Route::resource('users', InertiaUserController::class)->names([
                'index' => 'inertia.users.index',
                'create' => 'inertia.users.create',
                'store' => 'inertia.users.store',
                'show' => 'inertia.users.show',
                'edit' => 'inertia.users.edit',
                'update' => 'inertia.users.update',
                'destroy' => 'inertia.users.destroy',
            ]);
            Route::get('userrole', [InertiaUserRoleController::class, 'index'])->name('inertia.user-roles.index');
        });
    });
});
