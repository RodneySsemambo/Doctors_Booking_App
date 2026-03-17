<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Withdrawal;
use Illuminate\Auth\Access\HandlesAuthorization;

class WithdrawalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_withdrawal');
    }

    public function view(AuthUser $authUser, Withdrawal $withdrawal): bool
    {
        return $authUser->can('view_withdrawal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_withdrawal');
    }

    public function update(AuthUser $authUser, Withdrawal $withdrawal): bool
    {
        return $authUser->can('update_withdrawal');
    }

    public function delete(AuthUser $authUser, Withdrawal $withdrawal): bool
    {
        return $authUser->can('delete_withdrawal');
    }

    public function restore(AuthUser $authUser, Withdrawal $withdrawal): bool
    {
        return $authUser->can('restore_withdrawal');
    }

    public function forceDelete(AuthUser $authUser, Withdrawal $withdrawal): bool
    {
        return $authUser->can('force_delete_withdrawal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_withdrawal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_withdrawal');
    }

    public function replicate(AuthUser $authUser, Withdrawal $withdrawal): bool
    {
        return $authUser->can('replicate_withdrawal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_withdrawal');
    }

}