<?php

declare(strict_types=1);

namespace App\Policies\Core;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Core\UserStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserStatusPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any::user_status');
    }

    public function view(AuthUser $authUser, UserStatus $userStatus): bool
    {
        return $authUser->can('view::user_status');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create::user_status');
    }

    public function update(AuthUser $authUser, UserStatus $userStatus): bool
    {
        return $authUser->can('update::user_status');
    }

    public function delete(AuthUser $authUser, UserStatus $userStatus): bool
    {
        return $authUser->can('delete::user_status');
    }

    public function restore(AuthUser $authUser, UserStatus $userStatus): bool
    {
        return $authUser->can('restore::user_status');
    }

    public function forceDelete(AuthUser $authUser, UserStatus $userStatus): bool
    {
        return $authUser->can('force_delete::user_status');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any::user_status');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any::user_status');
    }

    public function replicate(AuthUser $authUser, UserStatus $userStatus): bool
    {
        return $authUser->can('replicate::user_status');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder::user_status');
    }

}