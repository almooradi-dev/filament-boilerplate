<?php

namespace App\Policies\Core;

use App\Models\Core\Page;
use App\Models\User;

class PagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('viewAny_Page');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Page $model): bool
    {
        return $user->hasPermissionTo('view_Page');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_Page');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Page $model): bool
    {
        return $user->hasPermissionTo('update_Page');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Page $model): bool
    {
        return $user->hasPermissionTo('delete_Page');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Page $model): bool
    {
        return $user->hasPermissionTo('restore_Page');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Page $model): bool
    {
        return $user->hasPermissionTo('forceDelete_Page');
    }
}
