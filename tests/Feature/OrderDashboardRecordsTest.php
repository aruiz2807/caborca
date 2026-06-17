<?php

use App\Enums\OrderStatus;
use App\Models\Dependency;
use App\Models\Location;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

test('dashboard records endpoint requires view-home-dashboard permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('orders.dashboard_records', ['filter' => 'all']))
        ->assertForbidden();

    $user->givePermissionTo('view-home-dashboard');

    $this->actingAs($user)
        ->get(route('orders.dashboard_records', ['filter' => 'all']))
        ->assertOk();
});

test('dashboard records endpoint validates filter parameter', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('view-home-dashboard');

    // Missing filter
    $this->actingAs($user)
        ->getJson(route('orders.dashboard_records'))
        ->assertStatus(422);

    // Invalid filter value
    $this->actingAs($user)
        ->getJson(route('orders.dashboard_records', ['filter' => 'invalid_status']))
        ->assertStatus(422);
});

test('dashboard records endpoint returns filtered orders', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('view-home-dashboard');

    $currentYear = now()->year;

    // Create necessary dependency setup
    $location = Location::create(['name' => 'Centro']);
    $dependency = Dependency::create([
        'name' => 'Dependency Test',
        'customer_number' => 'CUST-1234',
        'location_id' => $location->id,
        'user_id' => $user->id,
    ]);

    // Create necessary service type setup
    $service = Service::create(['name' => 'General Maintenance']);

    // Create 3 orders: 1 requested, 1 entered (both are pending), 1 finished (attended)
    $pendingOrder1 = Order::create([
        'purchase_order' => 'PO-PEND1',
        'economic_number' => 'ECO-PEND1',
        'order_file' => 'temp.pdf',
        'vehicle_dependency_id' => $dependency->id,
        'vehicle_vin' => '12345678901234567',
        'vehicle_description' => 'Pending Car 1',
        'vehicle_plate' => 'PEND1',
        'vehicle_model' => '2026',
        'vehicle_brand_id' => 1,
        'service_type_id' => $service->id,
        'service_requested_date' => "{$currentYear}-06-17",
        'service_location_id' => $location->id,
        'service_description' => 'Service Pending 1',
        'status' => OrderStatus::REQUESTED,
    ]);

    $pendingOrder2 = Order::create([
        'purchase_order' => 'PO-PEND2',
        'economic_number' => 'ECO-PEND2',
        'order_file' => 'temp.pdf',
        'vehicle_dependency_id' => $dependency->id,
        'vehicle_vin' => '12345678901234568',
        'vehicle_description' => 'Pending Car 2',
        'vehicle_plate' => 'PEND2',
        'vehicle_model' => '2026',
        'vehicle_brand_id' => 1,
        'service_type_id' => $service->id,
        'service_requested_date' => "{$currentYear}-06-17",
        'service_location_id' => $location->id,
        'service_description' => 'Service Pending 2',
        'status' => OrderStatus::ENTERED,
    ]);

    $attendedOrder = Order::create([
        'purchase_order' => 'PO-ATT1',
        'economic_number' => 'ECO-ATT1',
        'order_file' => 'temp.pdf',
        'vehicle_dependency_id' => $dependency->id,
        'vehicle_vin' => '12345678901234569',
        'vehicle_description' => 'Attended Car',
        'vehicle_plate' => 'ATT1',
        'vehicle_model' => '2026',
        'vehicle_brand_id' => 1,
        'service_type_id' => $service->id,
        'service_requested_date' => "{$currentYear}-06-17",
        'service_location_id' => $location->id,
        'service_description' => 'Service Attended',
        'status' => OrderStatus::FINISHED,
    ]);

    // Test filter = 'all'
    $responseAll = $this->actingAs($user)
        ->getJson(route('orders.dashboard_records', ['filter' => 'all']))
        ->assertOk();
    
    expect($responseAll->json('orders'))->toHaveCount(3);

    // Test filter = 'pending'
    $responsePending = $this->actingAs($user)
        ->getJson(route('orders.dashboard_records', ['filter' => 'pending']))
        ->assertOk();
    
    expect($responsePending->json('orders'))->toHaveCount(2);
    $pendingPurchaseOrders = collect($responsePending->json('orders'))->pluck('purchase_order')->toArray();
    expect($pendingPurchaseOrders)->toContain('PO-PEND1', 'PO-PEND2');

    // Test filter = 'attended'
    $responseAttended = $this->actingAs($user)
        ->getJson(route('orders.dashboard_records', ['filter' => 'attended']))
        ->assertOk();
    
    expect($responseAttended->json('orders'))->toHaveCount(1);
    expect($responseAttended->json('orders.0.purchase_order'))->toEqual('PO-ATT1');
});
