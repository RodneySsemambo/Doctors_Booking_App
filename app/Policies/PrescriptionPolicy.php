<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Prescription;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrescriptionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_prescription');
    }

    public function view(AuthUser $authUser, Prescription $prescription): bool
    {
        return $authUser->can('view_prescription');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_prescription');
    }

    public function update(AuthUser $authUser, Prescription $prescription): bool
    {
        return $authUser->can('update_prescription');
    }

    public function delete(AuthUser $authUser, Prescription $prescription): bool
    {
        return $authUser->can('delete_prescription');
    }

    public function restore(AuthUser $authUser, Prescription $prescription): bool
    {
        return $authUser->can('restore_prescription');
    }

    public function forceDelete(AuthUser $authUser, Prescription $prescription): bool
    {
        return $authUser->can('force_delete_prescription');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_prescription');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_prescription');
    }

    public function replicate(AuthUser $authUser, Prescription $prescription): bool
    {
        return $authUser->can('replicate_prescription');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_prescription');
    }

}