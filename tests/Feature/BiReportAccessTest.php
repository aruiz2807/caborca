<?php

use App\Models\BiReport;
use App\Models\BiSection;
use App\Models\User;
use Spatie\Permission\Models\Permission;

test('user with section and report permissions can view BI report page', function () {
    $section = BiSection::query()->create([
        'name' => 'Postventa',
        'slug' => 'postventa',
    ]);

    $report = BiReport::query()->create([
        'bi_section_id' => $section->id,
        'name' => 'Ordenes',
        'slug' => 'ordenes',
        'embed_url' => 'https://app.powerbi.com/view?r=abc',
    ]);

    Permission::query()->firstOrCreate([
        'name' => $section->permissionName(),
        'guard_name' => 'web',
    ]);
    Permission::query()->firstOrCreate([
        'name' => $report->permissionName(),
        'guard_name' => 'web',
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo([$section->permissionName(), $report->permissionName()]);

    $this->actingAs($user)
        ->get(route('reports.show', $report->id))
        ->assertOk();
});

test('user without section permission cannot view BI report page', function () {
    $section = BiSection::query()->create([
        'name' => 'Postventa',
        'slug' => 'postventa',
    ]);

    $report = BiReport::query()->create([
        'bi_section_id' => $section->id,
        'name' => 'Ordenes',
        'slug' => 'ordenes',
        'embed_url' => 'https://app.powerbi.com/view?r=abc',
    ]);

    Permission::query()->firstOrCreate([
        'name' => $section->permissionName(),
        'guard_name' => 'web',
    ]);
    Permission::query()->firstOrCreate([
        'name' => $report->permissionName(),
        'guard_name' => 'web',
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo([$report->permissionName()]);

    $this->actingAs($user)
        ->get(route('reports.show', $report->id))
        ->assertForbidden();
});

