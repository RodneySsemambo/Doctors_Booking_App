<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Hospital;
use Illuminate\Auth\Access\HandlesAuthorization;

class HospitalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_hospital');
    }

    public function view(AuthUser $authUser, Hospital $hospital): bool
    {
        return $authUser->can('view_hospital');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_hospital');
    }

    public function update(AuthUser $authUser, Hospital $hospital): bool
    {
        return $authUser->can('update_hospital');
    }

    public function delete(AuthUser $authUser, Hospital $hospital): bool
    {
        return $authUser->can('delete_hospital');
    }

    public function restore(AuthUser $authUser, Hospital $hospital): bool
    {
        return $authUser->can('restore_hospital');
    }

    public function forceDelete(AuthUser $authUser, Hospital $hospital): bool
    {
        return $authUser->can('force_delete_hospital');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_hospital');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_hospital');
    }

    public function replicate(AuthUser $authUser, Hospital $hospital): bool
    {
        return $authUser->can('replicate_hospital');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_hospital');
    }

}