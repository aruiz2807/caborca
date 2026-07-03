<?php

use App\Models\User;

test('users are redirected to the application root when logging out', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect(route('welcome'));
    $this->assertGuest();
});
