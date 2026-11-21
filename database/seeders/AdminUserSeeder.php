<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\AdminUser;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Rolları yarat (Yalnız bir dəfə və updateOrCreate ilə)
        $superAdmin = Role::updateOrCreate(
            ['name' => 'super_admin'],
            ['display_name' => 'Super Admin']
        );

        $manager = Role::updateOrCreate(
            ['name' => 'manager'],
            ['display_name' => 'Manager']
        );

        // 2. İcazələri yarat
        $permissions = [
            // Settings səhifələri üçün icazələr
            'access_main_settings',
            'access_contact_information', 
            'access_social_media',
            
            // User management üçün icazələr
            'access_admin_users',
            'access_roles',
        ];

        foreach ($permissions as $permissionName) {
            Permission::updateOrCreate(
                ['name' => $permissionName],
                [
                    'display_name' => ucwords(str_replace('_', ' ', $permissionName)),
                    'module' => 'admin',
                ]
            );
        }

        // 3. Super Admin roluna bütün icazələri ver
        $allPermissions = Permission::whereIn('name', $permissions)->get();
        $superAdmin->permissions()->sync($allPermissions->pluck('id'));

        // 4. Manager roluna yalnız settings icazələri ver (admin users və roles olmadan)
        $managerPermissions = Permission::whereIn('name', [
            'access_main_settings',
            'access_contact_information', 
            'access_social_media',
        ])->get();
        $manager->permissions()->sync($managerPermissions->pluck('id'));

        // 5. Admin istifadəçilərini yarat
        AdminUser::updateOrCreate(
            ['email' => 'admin@codemark.az'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'role_id' => $superAdmin->id,
            ]
        );

        AdminUser::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Elnur',
                'password' => Hash::make('admin123'),
                'role_id' => $superAdmin->id,
            ]
        );
    }
}
