<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('login screen generates https urls behind trusted proxies', function () {
    $response = $this
        ->withServerVariables([
            'HTTP_HOST' => 'caborcaautomotriz.qualisys.mx',
            'HTTP_X_FORWARDED_HOST' => 'caborcaautomotriz.qualisys.mx',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PREFIX' => '/citas',
        ])
        ->get('/login');

    $response->assertOk();
    $response->assertSee('https://caborcaautomotriz.qualisys.mx/citas', false);
    $response->assertDontSee('http://caborcaautomotriz.qualisys.mx/citas', false);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));
});

test('users cannot authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
