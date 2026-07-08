<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

test('brands are loaded through the dedicated orders endpoint', function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    Permission::firstOrCreate([
        'name' => 'view-orders-active',
        'guard_name' => 'web',
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo('view-orders-active');

    Cache::flush();

    Config::set('api.api_key', 'test-token');
    Config::set('api.api_url', 'https://api.example.test');

    Http::fake([
        '*dynamic/marcas*' => Http::response([
            'data' => [
                [
                    'ID' => 1,
                    'DESCRIPCION' => 'Marca de prueba',
                ],
            ],
        ], 200),
    ]);

    $response = $this->actingAs($user)->get(route('orders.brands'));

    $response->assertOk();
    $response->assertJsonPath('brands.0.ID', 1);
    $response->assertJsonPath('brands.0.DESCRIPCION', 'Marca de prueba');
});
