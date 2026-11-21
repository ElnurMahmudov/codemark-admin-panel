<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\Permission;

class UpdateManagerPermissions extends Command
{
    protected $signature = 'app:update-manager-permissions';
    protected $description = 'Update manager role with settings permissions';

    public function handle()
    {
        $this->info('Updating manager permissions...');
        
        $manager = Role::where('name', 'manager')->first();
        
        if (!$manager) {
            $this->error('Manager role not found!');
            return 1;
        }
        
        // Settings page permissions
        $settingsPermissions = [
            'page_main_settings_page',
            'page_contact_information_page',
            'page_social_media_page',
        ];
        
        foreach ($settingsPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $manager->permissions()->syncWithoutDetaching([$permission->id]);
                $this->info("Added permission: {$permissionName}");
            } else {
                $this->warn("Permission not found: {$permissionName}");
            }
        }
        
        $this->info('Manager permissions updated successfully!');
        return 0;
    }
}
