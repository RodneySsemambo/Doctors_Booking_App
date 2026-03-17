<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class PagePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    public function view(User $user, $page): bool
    {
        Log::info('PagePolicy view check', [
            'user' => $user->email,
            'page' => class_basename($page),
            'can' => $user->can('view_page_' . class_basename($page))
        ]);

        $pageClass = class_basename($page);
        $permissionName = 'view_page_' . $pageClass;
        return $user->can($permissionName);
        // Get the page class name without namespace
        $pageClass = class_basename($page);
        $permissionName = 'view_page_' . $pageClass;

        // Check if user has this permission
        return $user->can($permissionName);
    }

    /**
     * Handle all other methods through __call
     */
    public function __call($method, $arguments)
    {
        $user = $arguments[0];
        $page = $arguments[1] ?? null;

        if ($page) {
            $pageClass = class_basename($page);
            $permissionName = $method . '_page_' . $pageClass;
            return $user->can($permissionName);
        }

        return false;
    }
}
