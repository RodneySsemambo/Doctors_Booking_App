<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Appointment;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_appointment');
    }

    public function view(AuthUser $authUser, Appointment $appointment): bool
    {
        return $authUser->can('view_appointment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_appointment');
    }

    public function update(AuthUser $authUser, Appointment $appointment): bool
    {
        return $authUser->can('update_appointment');
    }

    public function delete(AuthUser $authUser, Appointment $appointment): bool
    {
        return $authUser->can('delete_appointment');
    }

    public function restore(AuthUser $authUser, Appointment $appointment): bool
    {
        return $authUser->can('restore_appointment');
    }

    public function forceDelete(AuthUser $authUser, Appointment $appointment): bool
    {
        return $authUser->can('force_delete_appointment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_appointment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_appointment');
    }

    public function replicate(AuthUser $authUser, Appointment $appointment): bool
    {
        return $authUser->can('replicate_appointment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_appointment');
    }

}