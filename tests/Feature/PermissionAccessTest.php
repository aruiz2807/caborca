<?php

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

test('home requires view-home-dashboard permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertForbidden();

    $user->givePermissionTo('view-home-dashboard');

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk();
});

test('locations index requires view-locations permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('locations.index'))
        ->assertForbidden();

    $user->givePermissionTo('view-locations');

    $this->actingAs($user)
        ->get(route('locations.index'))
        ->assertOk();
});

test('locations store requires create-location permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('locations.store'), [
            'name' => 'Centro',
        ])
        ->assertForbidden();

    $user->givePermissionTo('create-location');

    $this->actingAs($user)
        ->post(route('locations.store'), [
            'name' => 'Centro',
        ])
        ->assertRedirect(route('locations.index'));

    $this->assertDatabaseHas('locations', [
        'name' => 'Centro',
    ]);
});

test('updating role permissions requires manage-role-permissions permission', function () {
    $user = User::factory()->create();
    $role = Role::create([
        'name' => 'Operador',
        'description' => 'Rol de prueba',
        'guard_name' => 'web',
    ]);

    $this->actingAs($user)
        ->put(route('roles.update_permissions', $role->id), [
            'permissions' => ['view-home-dashboard'],
        ])
        ->assertForbidden();

    $user->givePermissionTo('manage-role-permissions');

    $this->actingAs($user)
        ->put(route('roles.update_permissions', $role->id), [
            'permissions' => ['view-home-dashboard'],
        ])
        ->assertRedirect(route('roles.index'));

    expect($role->fresh()->hasPermissionTo('view-home-dashboard'))->toBeTrue();
});
