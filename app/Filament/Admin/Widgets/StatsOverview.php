<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calculate stats
        $todayAppointments = Appointment::whereDate('appointment_date', today())->count();
        $totalRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        $activeDoctors = Doctor::where('is_verified', true)->count();
        $totalPatients = Patient::count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $completedToday = Appointment::whereDate('appointment_date', today())
            ->where('status', 'completed')
            ->count();
        $averageRating = DB::table('reviews')->avg('rating');
        $pendingPayments = Payment::where('status', 'pending')->sum('amount');

        return [
            Stat::make('Today\'s Appointments', $todayAppointments)
                ->description('Scheduled for today')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Monthly Revenue', 'UGX ' . number_format($totalRevenue, 0))
                ->description('Total revenue this month')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([15, 20, 18, 22, 25, 23, 28, 30]),

            Stat::make('Active Doctors', $activeDoctors)
                ->description('Verified and active')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Total Patients', $totalPatients)
                ->description('Registered patients')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Pending Appointments', $pendingAppointments)
                ->description('Awaiting confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Completed Today', $completedToday)
                ->description('Appointments completed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),


        ];
    }
}
