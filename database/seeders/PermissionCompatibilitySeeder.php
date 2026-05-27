<?php

namespace Database\Seeders;

use App\Models\Role;
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

        $knownPermissions = Permission::query()->pluck('name')->values()->all();
        $knownPermissionsLookup = array_fill_keys($knownPermissions, true);
        $rules = config('permission_compatibility_map.rules', []);

        if (! is_array($rules) || empty($rules)) {
            return;
        }

        $missingPermissions = [];

        Role::query()->with('permissions')->chunkById(100, function ($roles) use (
            $rules,
            $knownPermissionsLookup,
            &$missingPermissions
        ): void {
            foreach ($roles as $role) {
                $rolePermissions = $role->permissions->pluck('name')->all();
                $rolePermissionsLookup = array_fill_keys($rolePermissions, true);
                $permissionsToGrant = [];

                foreach ($rules as $rule) {
                    if (! is_array($rule)) {
                        continue;
                    }

                    $ifAny = $this->normalizePermissions($rule['if_any'] ?? []);
                    $grant = $this->normalizePermissions($rule['grant'] ?? []);

                    if (empty($ifAny) || empty($grant)) {
                        continue;
                    }

                    $hasAnyTriggerPermission = false;

                    foreach ($ifAny as $permissionName) {
                        if (isset($rolePermissionsLookup[$permissionName])) {
                            $hasAnyTriggerPermission = true;
                            break;
                        }
                    }

                    if (! $hasAnyTriggerPermission) {
                        continue;
                    }

                    foreach ($grant as $permissionName) {
                        if (! isset($knownPermissionsLookup[$permissionName])) {
                            $missingPermissions[$permissionName] = true;
                            continue;
                        }

                        if (isset($rolePermissionsLookup[$permissionName])) {
                            continue;
                        }

                        $permissionsToGrant[] = $permissionName;
                        $rolePermissionsLookup[$permissionName] = true;
                    }
                }

                if (! empty($permissionsToGrant)) {
                    $role->givePermissionTo(array_values(array_unique($permissionsToGrant)));
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

    /**
     * @param  mixed  $permissions
     * @return array<int, string>
     */
    protected function normalizePermissions(mixed $permissions): array
    {
        if (! is_array($permissions)) {
            return [];
        }

        return array_values(array_unique(array_filter($permissions, function ($permissionName) {
            return is_string($permissionName) && $permissionName !== '';
        })));
    }
}
