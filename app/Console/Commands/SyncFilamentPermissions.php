<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

class SyncFilamentPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-filament-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $resources = Filament::getResources();
        
        foreach ($resources as $resource) {
            $name = Str::snake(class_basename($resource)); // məs: role_resource
            $displayName = str_replace(' Resource', '', class_basename($resource));

            // Hər resurs üçün standart 4 icazə yaradırıq
            $actions = [
                'view' => 'View',
                'create' => 'Create',
                'update' => 'Update',
                'delete' => 'Delete'
            ];

            foreach ($actions as $key => $label) {
                Permission::updateOrCreate(
                    ['name' => "{$name}_{$key}"],
                    [
                        'display_name' => "{$displayName}: {$label}",
                        'module' => $displayName
                    ]
                );
            }
        }

        // Bütün xüsusi səhifələri (Pages) götürürük
        $pages = Filament::getPages();
        foreach ($pages as $page) {
            $pageName = Str::snake(class_basename($page));
            
            Permission::updateOrCreate(
                ['name' => "page_{$pageName}"],
                [
                    'display_name' => "Page: " . class_basename($page),
                    'module' => 'Custom Pages'
                ]
            );
        }

        $this->info('Filament permissions have been synchronized successfully!');
    }
}
