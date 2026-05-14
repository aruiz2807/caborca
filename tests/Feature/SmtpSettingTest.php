<?php

use App\Models\Role;
use App\Models\SmtpSetting;
use App\Models\User;

test('super admin can view smtp settings page', function () {
    $user = User::factory()->create();
    Role::create([
        'name' => 'Super-Admin',
        'guard_name' => 'web',
        'description' => 'Acceso total',
    ]);
    $user->assignRole('Super-Admin');

    $response = $this->actingAs($user)->get(route('smtp.index'));

    $response->assertOk();
});

test('super admin can store smtp settings', function () {
    $user = User::factory()->create();
    Role::create([
        'name' => 'Super-Admin',
        'guard_name' => 'web',
        'description' => 'Acceso total',
    ]);
    $user->assignRole('Super-Admin');

    $response = $this->actingAs($user)->put(route('smtp.update'), [
        'provider' => 'smtp',
        'host' => 'smtp.sendgrid.net',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'apikey',
        'password' => 'super-secret',
        'from_name' => 'Caborca',
        'from_email' => 'notificaciones@caborca.test',
        'active' => 1,
    ]);

    $response->assertRedirect(route('smtp.index'));

    $setting = SmtpSetting::first();

    expect($setting)->not->toBeNull();
    expect($setting->provider)->toBe('smtp');
    expect($setting->host)->toBe('smtp.sendgrid.net');
    expect($setting->from_email)->toBe('notificaciones@caborca.test');
    expect($setting->decrypted_password)->toBe('super-secret');
});
