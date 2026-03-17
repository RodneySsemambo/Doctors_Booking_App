<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Widgets\AppointmentsChart;
use App\Filament\Admin\Widgets\RevenueVsWithdrawalsChartWidget;
use App\Filament\Admin\Widgets\WithdrawalChartWidget;
use App\Filament\Admin\Widgets\WithdrawalStatsWidget;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use App\Filament\Admin\Widgets\AppointmentStatusChart;
use App\Filament\Admin\Widgets\LastAppointments;
use App\Filament\Admin\Widgets\RevenueChart;
use App\Filament\Admin\Widgets\SpecializationChart;
use App\Filament\Admin\Widgets\TopDoctors;
use App\Filament\Admin\Pages\Reports;
use App\Filament\Admin\Pages\Settings;
use App\Filament\Admin\Pages\WithdrawalAnalytics;
use App\Filament\Admin\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->default()
            ->brandName('Health Care')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
                Reports::class,
                Settings::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                StatsOverview::class,
                WithdrawalStatsWidget::class,
                AppointmentsChart::class,
                RevenueChart::class,
                WithdrawalChartWidget::class,
                RevenueVsWithdrawalsChartWidget::class,
                LastAppointments::class
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
