<?php

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Permission;

test('creating an order without an assigned dependency returns a controlled error', function () {
    Permission::firstOrCreate([
        'name' => 'create-order',
        'guard_name' => 'web',
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo('create-order');

    Config::set('api.api_key', 'test-token');
    Config::set('api.api_url', 'https://example.test');

    $response = $this->actingAs($user)->post(route('orders.store'), [
        'purchase_order' => 'PO-1001',
        'economic_number' => 'ECO-1001',
        'vehicle_vin' => '12345678901234567',
        'vehicle_description' => 'Vehiculo de prueba',
        'vehicle_plate' => 'ABC123',
        'vehicle_model' => '2026',
        'vehicle_brand' => '1',
        'service_type' => '1',
        'service_date' => now()->toDateString(),
        'service_location' => '1',
        'service_description' => 'Solicitud de prueba',
        'vehicle_dependency_id' => null,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'No se encontró una dependencia asociada. Busque el vehículo para autocompletar la dependencia o asigne una al usuario.');
});
