<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Specialization;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpecializationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_specialization');
    }

    public function view(AuthUser $authUser, Specialization $specialization): bool
    {
        return $authUser->can('view_specialization');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_specialization');
    }

    public function update(AuthUser $authUser, Specialization $specialization): bool
    {
        return $authUser->can('update_specialization');
    }

    public function delete(AuthUser $authUser, Specialization $specialization): bool
    {
        return $authUser->can('delete_specialization');
    }

    public function restore(AuthUser $authUser, Specialization $specialization): bool
    {
        return $authUser->can('restore_specialization');
    }

    public function forceDelete(AuthUser $authUser, Specialization $specialization): bool
    {
        return $authUser->can('force_delete_specialization');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_specialization');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_specialization');
    }

    public function replicate(AuthUser $authUser, Specialization $specialization): bool
    {
        return $authUser->can('replicate_specialization');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_specialization');
    }

}