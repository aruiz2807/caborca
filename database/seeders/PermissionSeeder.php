<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'create-order',
            'create-appointment',
            'create-location',
            'create-dependency',
            'create-workshop',
            'create-service-type',

            'cancel-order',
            'cancel-appointment',
            
            'delete-location',
            'delete-dependency',
            'delete-workshop',
            'delete-service-type',            
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
