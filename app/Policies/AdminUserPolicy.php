<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdminUserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(AdminUser $user): bool
    {
        return $user->hasPermission('admin_user_resource_view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(AdminUser $user, AdminUser $adminUser): bool
    {
        return $user->hasPermission('admin_user_resource_view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(AdminUser $user): bool
    {
        return $user->hasPermission('admin_user_resource_create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(AdminUser $user, AdminUser $adminUser): bool
    {
        return $user->hasPermission('admin_user_resource_update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(AdminUser $user, AdminUser $adminUser): bool
    {
        return $user->id !== $adminUser->id && $user->hasPermission('admin_user_resource_delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(AdminUser $user, AdminUser $adminUser): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(AdminUser $user, AdminUser $adminUser): bool
    {
        return false;
    }
}
