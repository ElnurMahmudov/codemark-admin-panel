<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\AdminUser;
use App\Policies\AdminUserPolicy;
use App\Models\Role;
use App\Policies\RolePolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Policy-ləri manual bağlayaq (Zəmanət üçün)
        Gate::policy(AdminUser::class, AdminUserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        
        // Super Admin-i bütün Policy-lərdən əvvəl tanıyaq
        Gate::before(function ($user, $ability) {
            return $user->role?->name === 'super_admin' ? true : null;
        });

        // Curator default directory configuration
        \Awcodes\Curator\Components\Forms\CuratorPicker::configureUsing(static function (\Awcodes\Curator\Components\Forms\CuratorPicker $component) {
            $component->directory('media');
        });

        \Awcodes\Curator\Facades\Curator::directory('media');
    }
}
