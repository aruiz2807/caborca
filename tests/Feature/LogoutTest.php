<?php

use App\Models\User;

test('users are redirected to the application root when logging out', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect(route('welcome').'/');
    $this->assertGuest();
});

test('users are redirected to the current base path when logging out behind a subdirectory alias', function () {
    $user = User::factory()->create();

    $response = $this
        ->withServerVariables([
            'HTTP_HOST' => 'caborcaautomotriz.qualisys.mx',
            'HTTP_X_FORWARDED_HOST' => 'caborcaautomotriz.qualisys.mx',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PREFIX' => '/citas',
        ])
        ->actingAs($user)
        ->post('/logout');

    $response->assertRedirect('https://caborcaautomotriz.qualisys.mx/citas/');
    $this->assertGuest();
});
