<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Services\PermissionCompatibilityService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionCompatibilitySeeder extends Seeder
{
    /**
     * Backfill "view-*" permissions for roles that already have action permissions.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $compatibilityService = app(PermissionCompatibilityService::class);
        $knownPermissionsLookup = array_fill_keys(
            Permission::query()->pluck('name')->values()->all(),
            true
        );
        $rules = config('permission_compatibility_map.rules', []);

        if (! is_array($rules) || empty($rules)) {
            return;
        }

        $missingPermissions = [];

        Role::query()->with('permissions')->chunkById(100, function ($roles) use (
            $compatibilityService,
            $knownPermissionsLookup,
            &$missingPermissions
        ): void {
            foreach ($roles as $role) {
                $resolution = $compatibilityService->resolve(
                    $role->permissions->pluck('name')->all(),
                    $knownPermissionsLookup
                );

                foreach ($resolution['missing'] as $permissionName) {
                    $missingPermissions[$permissionName] = true;
                }

                if (! empty($resolution['grants'])) {
                    $role->givePermissionTo($resolution['grants']);
                }
            }
        });

        if (! empty($missingPermissions) && $this->command) {
            $this->command->warn(
                'PermissionCompatibilitySeeder: permisos no encontrados y omitidos: '
                .implode(', ', array_keys($missingPermissions))
            );
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
