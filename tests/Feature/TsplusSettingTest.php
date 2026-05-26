<?php

use App\Models\Role;
use App\Models\TsplusSetting;
use App\Models\User;

test('super admin can view tsplus settings page', function () {
    $user = User::factory()->create();
    Role::create([
        'name' => 'Super-Admin',
        'guard_name' => 'web',
        'description' => 'Acceso total',
    ]);
    $user->assignRole('Super-Admin');

    $response = $this->actingAs($user)->get(route('tsplus-settings.index'));

    $response->assertOk();
});

test('super admin can store tsplus url', function () {
    $user = User::factory()->create();
    Role::create([
        'name' => 'Super-Admin',
        'guard_name' => 'web',
        'description' => 'Acceso total',
    ]);
    $user->assignRole('Super-Admin');

    $response = $this->actingAs($user)->put(route('tsplus-settings.update'), [
        'url' => 'https://tsplus.caborca.test/session',
    ]);

    $response->assertRedirect(route('tsplus-settings.index'));

    $setting = TsplusSetting::query()->first();

    expect($setting)->not->toBeNull();
    expect($setting->url)->toBe('https://tsplus.caborca.test/session');
});

test('authenticated user can view tsplus page', function () {
    TsplusSetting::query()->create([
        'url' => 'https://tsplus.caborca.test/session',
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('tsplus.index'));

    $response->assertOk();
});
