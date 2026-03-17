<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AdminWithdrawal;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminWithdrawalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_admin_withdrawal');
    }

    public function view(AuthUser $authUser, AdminWithdrawal $adminWithdrawal): bool
    {
        return $authUser->can('view_admin_withdrawal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_admin_withdrawal');
    }

    public function update(AuthUser $authUser, AdminWithdrawal $adminWithdrawal): bool
    {
        return $authUser->can('update_admin_withdrawal');
    }

    public function delete(AuthUser $authUser, AdminWithdrawal $adminWithdrawal): bool
    {
        return $authUser->can('delete_admin_withdrawal');
    }

    public function restore(AuthUser $authUser, AdminWithdrawal $adminWithdrawal): bool
    {
        return $authUser->can('restore_admin_withdrawal');
    }

    public function forceDelete(AuthUser $authUser, AdminWithdrawal $adminWithdrawal): bool
    {
        return $authUser->can('force_delete_admin_withdrawal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_admin_withdrawal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_admin_withdrawal');
    }

    public function replicate(AuthUser $authUser, AdminWithdrawal $adminWithdrawal): bool
    {
        return $authUser->can('replicate_admin_withdrawal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_admin_withdrawal');
    }

}