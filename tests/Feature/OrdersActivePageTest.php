<?php

use App\Enums\OrderStatus;
use App\Enums\Status;
use App\Models\Dependency;
use App\Models\Location;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

test('the active orders page renders without calling external services', function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    Permission::firstOrCreate([
        'name' => 'view-orders-active',
        'guard_name' => 'web',
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo('view-orders-active');

    $location = Location::create([
        'name' => 'Caborca',
        'status' => Status::ACTIVE,
    ]);

    $service = Service::create([
        'name' => 'Mantenimiento',
        'status' => Status::ACTIVE,
    ]);

    $workshop = Workshop::create([
        'name' => 'Taller Principal',
        'location_id' => $location->id,
        'database' => 'BASE-TEST',
        'status' => Status::ACTIVE,
    ]);

    $dependency = Dependency::create([
        'name' => 'Dependencia de prueba',
        'customer_number' => '1001',
        'location_id' => $location->id,
        'user_id' => $user->id,
        'status' => Status::ACTIVE,
    ]);

    Order::create([
        'purchase_order' => 'PO-1001',
        'economic_number' => 'ECO-1001',
        'order_file' => 'order.tmp',
        'vehicle_dependency_id' => $dependency->id,
        'vehicle_vin' => '12345678901234567',
        'vehicle_description' => 'Vehiculo de prueba',
        'vehicle_model' => '2026',
        'vehicle_plate' => 'ABC123',
        'vehicle_brand_id' => 1,
        'service_type_id' => $service->id,
        'service_requested_date' => now()->toDateString(),
        'service_location_id' => $location->id,
        'service_description' => 'Servicio de prueba',
        'appointment' => 'CITA-1001',
        'appointment_workshop_id' => $workshop->id,
        'status' => OrderStatus::SCHEDULED,
    ]);

    Cache::flush();
    Http::preventStrayRequests();

    $response = $this->actingAs($user)->get(route('orders.active'));

    $response->assertOk();
});
