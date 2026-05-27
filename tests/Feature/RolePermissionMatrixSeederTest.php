<?php

use App\Models\Role;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionMatrixSeeder;
use Spatie\Permission\Models\Permission;

test('role permission matrix seeder creates configured roles with expected permissions', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(RolePermissionMatrixSeeder::class);

    $matrix = config('role_permission_matrix.roles', []);

    foreach (array_keys($matrix) as $roleName) {
        expect(Role::query()->where('name', $roleName)->exists())->toBeTrue();
    }

    $asesor = Role::query()->where('name', 'Asesor')->first();
    $gobierno = Role::query()->where('name', 'Gobierno')->first();
    $admin = Role::query()->where('name', 'Admin')->first();

    expect($asesor)->not->toBeNull();
    expect($gobierno)->not->toBeNull();
    expect($admin)->not->toBeNull();

    expect($asesor->hasPermissionTo('view-orders-active'))->toBeTrue();
    expect($asesor->hasPermissionTo('create-appointment'))->toBeTrue();
    expect($gobierno->hasPermissionTo('create-order'))->toBeTrue();
    expect($gobierno->hasPermissionTo('create-appointment'))->toBeFalse();
    expect($admin->permissions()->count())->toBe(Permission::query()->count());
});

test('role permission matrix seeder is idempotent', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(RolePermissionMatrixSeeder::class);
    $this->seed(RolePermissionMatrixSeeder::class);

    expect(Role::query()->where('name', 'Admin')->count())->toBe(1);
    expect(Role::query()->where('name', 'Asesor')->count())->toBe(1);
    expect(Role::query()->where('name', 'Gobierno')->count())->toBe(1);
});
