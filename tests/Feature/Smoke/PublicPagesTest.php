<?php

dataset('public pages', [
    'login' => ['login', 'Login'],
    'register' => ['register', 'Register'],
    'password reset request' => ['password.request', 'Reset Password'],
]);

it('renders public pages as HTML', function (string $routeName, string $visibleText) {
    $response = $this->get(route($routeName));

    $response
        ->assertOk()
        ->assertHeader('content-type', 'text/html; charset=UTF-8')
        ->assertSee('<html', false)
        ->assertSee($visibleText);
})->with('public pages');

it('redirects guests from the root page to login', function () {
    $this->get('/')
        ->assertRedirect(route('login'))
        ->assertStatus(302);
});
