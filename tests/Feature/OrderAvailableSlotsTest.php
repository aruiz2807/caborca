<?php

use App\Enums\Status;
use App\Models\Location;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

test('available slots uses a workshop advisor when the current user has no bpro user', function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    Permission::firstOrCreate([
        'name' => 'view-orders-active',
        'guard_name' => 'web',
    ]);

    $user = User::factory()->create([
        'name' => 'Operador',
        'email' => 'operador@example.com',
        'type' => 'A',
        'status' => Status::ACTIVE,
    ]);

    $user->givePermissionTo('view-orders-active');

    $advisor = User::factory()->create([
        'name' => 'Asesor',
        'email' => 'asesor@example.com',
        'type' => 'A',
        'status' => Status::ACTIVE,
    ]);

    $advisor->forceFill([
        'bpro_user' => 'HOE',
    ])->save();

    $location = Location::create([
        'name' => 'Caborca',
        'status' => Status::ACTIVE,
    ]);

    $workshop = Workshop::create([
        'name' => 'Hermosillo Norte',
        'location_id' => $location->id,
        'database' => 'CHRCaborca_Matriz',
        'status' => Status::ACTIVE,
    ]);

    $workshop->advisors()->attach($advisor->id);

    Cache::flush();
    Config::set('api.api_key', 'test-token');
    Config::set('api.api_url', 'https://api.example.test');

    Http::fake(function (Request $request) {
        expect($request->method())->toBe('GET');
        expect($request->url())->toBe('https://api.example.test/api/dynamic/horarios');

        $payload = json_decode($request->body(), true);

        expect($payload)->toMatchArray([
            'fecha' => '19/08/2026',
            'asesor' => 'HOE',
            'base' => 'CHRCaborca_Matriz',
        ]);

        return Http::response([
            'data' => [
                ['CIT_HORCITA' => '08:20'],
                ['CIT_HORCITA' => '08:40'],
            ],
        ], 200);
    });

    $response = $this->actingAs($user)->get(route('orders.available_slots', [
        'workshop' => $workshop->id,
        'date' => '2026-08-19',
    ]));

    $response->assertOk();
    $response->assertJsonCount(2, 'slots');
    $response->assertJsonPath('slots.0.CIT_HORCITA', '08:20');
});
