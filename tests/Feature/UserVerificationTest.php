<?php

use App\Models\Role;
use App\Models\User;

test('admin can create a user with a manually verified email', function () {
    $superAdminRole = Role::create([
        'name' => 'Super-Admin',
        'guard_name' => 'web',
        'description' => 'Acceso total',
    ]);

    Role::create([
        'name' => 'Operador',
        'guard_name' => 'web',
        'description' => 'Rol de prueba',
    ]);

    $admin = User::factory()->create();
    $admin->assignRole($superAdminRole);

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Usuario Verificado',
            'email' => 'verificado@caborca.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'Operador',
            'type' => 'A',
            'bpro_user' => 'A1234',
            'email_verified' => true,
        ])
        ->assertRedirect(route('users.index'));

    $createdUser = User::query()->where('email', 'verificado@caborca.test')->first();

    expect($createdUser)->not->toBeNull();
    expect($createdUser->hasVerifiedEmail())->toBeTrue();
});

test('admin can create a user without manually verifying the email', function () {
    $superAdminRole = Role::create([
        'name' => 'Super-Admin',
        'guard_name' => 'web',
        'description' => 'Acceso total',
    ]);

    Role::create([
        'name' => 'Operador',
        'guard_name' => 'web',
        'description' => 'Rol de prueba',
    ]);

    $admin = User::factory()->create();
    $admin->assignRole($superAdminRole);

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Usuario Sin Verificar',
            'email' => 'sin-verificar@caborca.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'Operador',
            'type' => 'A',
            'bpro_user' => 'A1235',
            'email_verified' => false,
        ])
        ->assertRedirect(route('users.index'));

    $createdUser = User::query()->where('email', 'sin-verificar@caborca.test')->first();

    expect($createdUser)->not->toBeNull();
    expect($createdUser->hasVerifiedEmail())->toBeFalse();
});
