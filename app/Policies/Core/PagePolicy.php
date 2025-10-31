<?php

declare(strict_types=1);

namespace App\Policies\Core;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Core\Page;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any::page');
    }

    public function view(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('view::page');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create::page');
    }

    public function update(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('update::page');
    }

    public function delete(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('delete::page');
    }

    public function restore(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('restore::page');
    }

    public function forceDelete(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('force_delete::page');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any::page');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any::page');
    }

    public function replicate(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('replicate::page');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder::page');
    }

}