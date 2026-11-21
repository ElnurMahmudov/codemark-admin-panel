<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\AdminUser;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(AdminUser $user): bool
    {
        return $user->hasPermission('role_resource_view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(AdminUser $user, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(AdminUser $user): bool
    {
        return $user->hasPermission('role_resource_create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(AdminUser $user, Role $role): bool
    {
        return $user->hasPermission('role_resource_update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(AdminUser $user, Role $role): bool
    {
        return $user->hasPermission('role_resource_delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(AdminUser $user, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(AdminUser $user, Role $role): bool
    {
        return false;
    }
}
