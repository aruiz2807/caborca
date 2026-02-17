<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    /**
     * Create the super admin role and user.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Gets all permissions via Gate::before rule; see AppServiceProvider
        $superAdminRole = Role::create([
            'name' => 'Super-Admin',
            'guard_name' => 'web',
            'description' => 'Acceso a todas las funcionalidades',
        ]);

        //Create super admin user
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('supersecret'),
            'email_verified_at' => Carbon::now(),
        ]);

        //Assign super admin role to super admin user
        $user->assignRole($superAdminRole);
    }
}
