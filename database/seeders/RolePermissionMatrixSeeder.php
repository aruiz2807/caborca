<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionMatrixSeeder extends Seeder
{
    /**
     * Apply role-permission matrix from config/role_permission_matrix.php
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $allPermissionNames = Permission::query()->pluck('name')->values()->all();
        $knownPermissions = array_flip($allPermissionNames);
        $defaultMode = (string) config('role_permission_matrix.default_mode', 'sync');
        $roles = (array) config('role_permission_matrix.roles', []);

        foreach ($roles as $roleName => $definition) {
            if (! is_string($roleName) || $roleName === '') {
                continue;
            }

            $roleDefinition = is_array($definition) ? $definition : [];
            $description = $roleDefinition['description'] ?? null;
            $mode = strtolower((string) ($roleDefinition['mode'] ?? $defaultMode));
            $requestedPermissions = $roleDefinition['permissions'] ?? [];

            $resolvedPermissions = $this->resolvePermissions(
                is_array($requestedPermissions) ? $requestedPermissions : [],
                $allPermissionNames,
                $knownPermissions
            );

            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['description' => $description]
            );

            if (is_string($description) && $description !== '' && empty($role->description)) {
                $role->description = $description;
                $role->save();
            }

            if ($mode === 'append') {
                $role->givePermissionTo($resolvedPermissions);
                continue;
            }

            $role->syncPermissions($resolvedPermissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Resolve configured permissions to known permissions.
     *
     * @param  array<int, string>  $requestedPermissions
     * @param  array<int, string>  $allPermissionNames
     * @param  array<string, int>  $knownPermissions
     * @return array<int, string>
     */
    protected function resolvePermissions(
        array $requestedPermissions,
        array $allPermissionNames,
        array $knownPermissions
    ): array {
        if (in_array('*', $requestedPermissions, true)) {
            return $allPermissionNames;
        }

        $resolved = [];
        $missing = [];

        foreach ($requestedPermissions as $permissionName) {
            if (! is_string($permissionName) || $permissionName === '') {
                continue;
            }

            if (! isset($knownPermissions[$permissionName])) {
                $missing[] = $permissionName;
                continue;
            }

            $resolved[] = $permissionName;
        }

        if (! empty($missing) && $this->command) {
            $this->command->warn(
                'RolePermissionMatrixSeeder: permisos no encontrados y omitidos: '.implode(', ', $missing)
            );
        }

        return array_values(array_unique($resolved));
    }
}
