<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check permissions and roles in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking permissions...');
        
        $permissions = \App\Models\Permission::all();
        $this->info("Total permissions: " . $permissions->count());
        
        foreach ($permissions as $permission) {
            $this->line("- {$permission->name} ({$permission->display_name})");
        }
        
        $this->info("\nChecking roles...");
        $roles = \App\Models\Role::with('permissions')->get();
        
        foreach ($roles as $role) {
            $this->info("\nRole: {$role->name} ({$role->display_name})");
            $this->info("Permissions: " . $role->permissions->count());
            foreach ($role->permissions as $permission) {
                $this->line("  - {$permission->name}");
            }
        }
        
        return 0;
    }
}
