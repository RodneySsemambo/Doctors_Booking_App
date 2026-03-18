<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        'Spatie\Permission\Models\Role' => 'App\Policies\RolePolicy',
        'Spatie\Permission\Models\Permission' => 'App\Policies\PermissionPolicy',
        'Filament\Pages\Dashboard' => 'App\Policies\PagePolicy',
        'App\Filament\Admin\Pages\AdminWithdrawalPage' => 'App\Policies\PagePolicy',
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }
    }
}
