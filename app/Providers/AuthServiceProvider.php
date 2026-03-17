<?php

namespace App\Providers;

use App\Filament\Admin\Pages\AdminWithdrawalPage;
use App\Policies\PagePolicy;
use Filament\Pages\Dashboard;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Dashboard::class => PagePolicy::class,
        AdminWithdrawalPage::class => PagePolicy::class,
    ];


    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //$this->register();
    }
}
