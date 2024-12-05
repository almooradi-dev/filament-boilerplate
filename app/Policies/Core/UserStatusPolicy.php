<?php

namespace App\Policies\Core;

use App\Models\User;
use App\Models\Core\UserStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserStatusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('viewAny_UserStatus');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserStatus $userStatus): bool
    {
        return $user->can('view_UserStatus');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_UserStatus');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserStatus $userStatus): bool
    {
        return $user->can('update_UserStatus');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserStatus $userStatus): bool
    {
        return $user->can('delete_UserStatus');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('deleteAny_UserStatus');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, UserStatus $userStatus): bool
    {
        return $user->can('forceDelete_UserStatus');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('forceDeleteAny_UserStatus');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, UserStatus $userStatus): bool
    {
        return $user->can('restore_UserStatus');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restoreAny_UserStatus');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, UserStatus $userStatus): bool
    {
        return $user->can('replicate_UserStatus');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_UserStatus');
    }
}
