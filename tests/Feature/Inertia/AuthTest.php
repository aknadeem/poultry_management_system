<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutVite();
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
});

it('renders the inertia login page', function () {
    $this->get(route('inertia.login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
});

it('loads the hyper creative theme on inertia pages', function () {
    $this->get(route('inertia.login'))
        ->assertOk()
        ->assertSee('assets/css/config/creative/bootstrap.min.css', false)
        ->assertSee('assets/css/config/creative/app.min.css', false)
        ->assertSee('assets/js/app.min.js', false);
});

it('still renders the blade login page', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Login')
        ->assertSee('<html', false);
});

it('redirects guests from the inertia dashboard to inertia login', function () {
    $this->get(route('inertia.dashboard'))
        ->assertRedirect(route('inertia.login'));
});

it('logs a user in through inertia and shows the dashboard', function () {
    $user = User::factory()->create();

    $this->post(route('inertia.login'), [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('inertia.dashboard'));

    $this->assertAuthenticatedAs($user);

    $this->get(route('inertia.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('auth.user.id', $user->id)
            ->where('auth.user.email', $user->email)
        );
});

it('lets inertia follow a normal redirect after login', function () {
    $user = User::factory()->create();

    $this->from(route('inertia.login'))
        ->withHeaders(['X-Inertia' => 'true'])
        ->post(route('inertia.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect(route('inertia.dashboard'));
});

it('returns inertia validation errors for a failed login', function () {
    $user = User::factory()->create();

    $this->from(route('inertia.login'))
        ->post(route('inertia.login'), [
            'email' => $user->email,
            'password' => 'not-the-password',
        ])
        ->assertSessionHasErrors('email');
});

it('logs a user out through inertia', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('inertia.logout'))
        ->assertRedirect(route('inertia.login'));

    $this->assertGuest();
});

it('renders the inertia register page', function () {
    $this->get(route('inertia.register'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/Register'));
});

it('validates inertia registration', function () {
    $this->from(route('inertia.register'))
        ->post(route('inertia.register'), [])
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

it('registers a user through inertia with the same rules as blade', function () {
    $this->post(route('inertia.register'), [
        'name' => 'New Operator',
        'email' => 'operator@example.com',
        'password' => 'password1',
        'password_confirmation' => 'password1',
    ])
        ->assertRedirect(route('inertia.dashboard'));

    $user = User::query()->where('email', 'operator@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('New Operator')
        ->and($user->user_role_id)->toBeNull()
        ->and(Hash::check('password1', $user->password))->toBeTrue();

    $this->assertAuthenticatedAs($user);
});

it('renders the inertia forgot password page', function () {
    $this->get(route('inertia.password.request'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/ForgotPassword'));
});

it('renders the inertia reset password page', function () {
    $this->get(route('inertia.password.reset', ['token' => 'test-token', 'email' => 'user@example.com']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Auth/ResetPassword')
            ->where('token', 'test-token')
            ->where('email', 'user@example.com')
        );
});

it('resets a password through inertia with a valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('inertia.password.email'), [
        'email' => $user->email,
    ])->assertRedirect();

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
        $this->post(route('inertia.password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect(route('inertia.dashboard'));

        expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
        $this->assertAuthenticatedAs($user);

        return true;
    });
});

it('does not expose a fake inertia email verification flow', function () {
    expect(\Illuminate\Support\Facades\Route::has('inertia.verification.notice'))->toBeFalse()
        ->and(\Illuminate\Support\Facades\Route::has('inertia.verification.resend'))->toBeFalse();
});

it('does not block an unverified user from the inertia dashboard', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('inertia.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Dashboard'));
});

it('renders the inertia confirm password page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('inertia.password.confirm'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/ConfirmPassword'));
});
