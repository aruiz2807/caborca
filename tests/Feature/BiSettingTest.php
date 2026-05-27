<?php

use App\Models\BiReport;
use App\Models\BiSection;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

test('bi settings index requires view-bi-settings permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('bi-settings.index'))
        ->assertForbidden();

    $user->givePermissionTo('view-bi-settings');

    $this->actingAs($user)
        ->get(route('bi-settings.index'))
        ->assertOk();
});

test('authorized user can create report permissions and delete section in cascade', function () {
    $admin = User::factory()->create();
    $admin->givePermissionTo(['view-bi-settings', 'manage-bi-settings']);

    $viewerRole = Role::query()->create([
        'name' => 'Visualizador BI',
        'guard_name' => 'web',
        'description' => 'Rol para reportes BI',
    ]);
    $viewerUser = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('bi-settings.sections.store'), [
            'name' => 'Postventa',
        ])
        ->assertRedirect(route('bi-settings.index'));

    $section = BiSection::query()->first();

    expect($section)->not->toBeNull();

    $this->actingAs($admin)
        ->post(route('bi-settings.reports.store'), [
            'bi_section_id' => $section->id,
            'name' => 'Ordenes',
            'embed_url' => 'https://app.powerbi.com/reportEmbed?reportId=abc123',
            'role_ids' => [$viewerRole->id],
            'user_ids' => [$viewerUser->id],
        ])
        ->assertRedirect(route('bi-settings.index'));

    $report = BiReport::query()->first();

    expect($report)->not->toBeNull();
    expect(Permission::query()->where('name', $section->permissionName())->exists())->toBeTrue();
    expect(Permission::query()->where('name', $report->permissionName())->exists())->toBeTrue();

    $viewerRole->refresh();
    $viewerUser->refresh();

    expect($viewerRole->hasPermissionTo($section->permissionName()))->toBeTrue();
    expect($viewerRole->hasPermissionTo($report->permissionName()))->toBeTrue();
    expect($viewerUser->hasPermissionTo($report->permissionName()))->toBeTrue();

    $this->actingAs($admin)
        ->delete(route('bi-settings.sections.destroy', $section->id))
        ->assertRedirect(route('bi-settings.index'));

    expect(BiSection::query()->count())->toBe(0);
    expect(BiReport::query()->count())->toBe(0);
    expect(Permission::query()->where('name', $section->permissionName())->exists())->toBeFalse();
    expect(Permission::query()->where('name', $report->permissionName())->exists())->toBeFalse();
});

