<?php

use App\Models\Role;
use Database\Seeders\PermissionCompatibilitySeeder;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

test('permission compatibility seeder grants view permissions from action permissions', function () {
    $role = Role::create([
        'name' => 'Operador Legacy',
        'description' => 'Rol con permisos heredados',
        'guard_name' => 'web',
    ]);

    $role->givePermissionTo([
        'create-order',
        'create-location',
        'update-message',
    ]);

    expect($role->hasPermissionTo('view-orders-active'))->toBeFalse();
    expect($role->hasPermissionTo('view-locations'))->toBeFalse();
    expect($role->hasPermissionTo('view-messages'))->toBeFalse();

    $this->seed(PermissionCompatibilitySeeder::class);

    $role = $role->fresh();

    expect($role->hasPermissionTo('view-orders-active'))->toBeTrue();
    expect($role->hasPermissionTo('view-orders-archive'))->toBeTrue();
    expect($role->hasPermissionTo('view-locations'))->toBeTrue();
    expect($role->hasPermissionTo('view-messages'))->toBeTrue();
    expect($role->hasPermissionTo('view-users'))->toBeFalse();
});

test('permission compatibility seeder is idempotent and does not grant unrelated permissions', function () {
    $role = Role::create([
        'name' => 'Auditor Legacy',
        'description' => 'Solo lectura básica',
        'guard_name' => 'web',
    ]);

    $role->givePermissionTo(['view-home-dashboard']);

    $this->seed(PermissionCompatibilitySeeder::class);
    $firstCount = $role->fresh()->permissions()->count();

    $this->seed(PermissionCompatibilitySeeder::class);
    $secondCount = $role->fresh()->permissions()->count();

    expect($firstCount)->toBe($secondCount);
    expect($role->fresh()->permissions->pluck('name')->all())->toBe(['view-home-dashboard']);
});
