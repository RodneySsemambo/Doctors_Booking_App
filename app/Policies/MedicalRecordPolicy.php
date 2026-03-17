<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MedicalRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicalRecordPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_medical_record');
    }

    public function view(AuthUser $authUser, MedicalRecord $medicalRecord): bool
    {
        return $authUser->can('view_medical_record');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_medical_record');
    }

    public function update(AuthUser $authUser, MedicalRecord $medicalRecord): bool
    {
        return $authUser->can('update_medical_record');
    }

    public function delete(AuthUser $authUser, MedicalRecord $medicalRecord): bool
    {
        return $authUser->can('delete_medical_record');
    }

    public function restore(AuthUser $authUser, MedicalRecord $medicalRecord): bool
    {
        return $authUser->can('restore_medical_record');
    }

    public function forceDelete(AuthUser $authUser, MedicalRecord $medicalRecord): bool
    {
        return $authUser->can('force_delete_medical_record');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_medical_record');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_medical_record');
    }

    public function replicate(AuthUser $authUser, MedicalRecord $medicalRecord): bool
    {
        return $authUser->can('replicate_medical_record');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_medical_record');
    }

}