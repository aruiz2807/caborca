<?php

use App\Models\RdpSetting;
use App\Models\Role;
use App\Models\User;

test('super admin can view rdp settings page', function () {
    $user = User::factory()->create();
    Role::create([
        'name' => 'Super-Admin',
        'guard_name' => 'web',
        'description' => 'Acceso total',
    ]);
    $user->assignRole('Super-Admin');

    $response = $this->actingAs($user)->get(route('rdp-settings.index'));

    $response->assertOk();
});

test('super admin can store rdp url', function () {
    $user = User::factory()->create();
    Role::create([
        'name' => 'Super-Admin',
        'guard_name' => 'web',
        'description' => 'Acceso total',
    ]);
    $user->assignRole('Super-Admin');

    $response = $this->actingAs($user)->put(route('rdp-settings.update'), [
        'url' => 'https://rdp.caborca.test/session',
    ]);

    $response->assertRedirect(route('rdp-settings.index'));

    $setting = RdpSetting::query()->first();

    expect($setting)->not->toBeNull();
    expect($setting->url)->toBe('https://rdp.caborca.test/session');
});

test('authenticated user can view rdp page', function () {
    RdpSetting::query()->create([
        'url' => 'https://rdp.caborca.test/session',
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('rdp.index'));

    $response->assertOk();
});
