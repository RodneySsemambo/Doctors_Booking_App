<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Doctor;
use Illuminate\Auth\Access\HandlesAuthorization;

class DoctorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_doctor');
    }

    public function view(AuthUser $authUser, Doctor $doctor): bool
    {
        return $authUser->can('view_doctor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_doctor');
    }

    public function update(AuthUser $authUser, Doctor $doctor): bool
    {
        return $authUser->can('update_doctor');
    }

    public function delete(AuthUser $authUser, Doctor $doctor): bool
    {
        return $authUser->can('delete_doctor');
    }

    public function restore(AuthUser $authUser, Doctor $doctor): bool
    {
        return $authUser->can('restore_doctor');
    }

    public function forceDelete(AuthUser $authUser, Doctor $doctor): bool
    {
        return $authUser->can('force_delete_doctor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_doctor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_doctor');
    }

    public function replicate(AuthUser $authUser, Doctor $doctor): bool
    {
        return $authUser->can('replicate_doctor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_doctor');
    }

}