<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Withdrawal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WithdrawalStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $pending = Withdrawal::where('status', 'pending')->count();
        $pendingAmount = Withdrawal::where('status', 'pending')->sum('net_amount');
        $processing = Withdrawal::where('status', 'processing')->count();
        $processingAmount = Withdrawal::where('status', 'processing')->sum('net_amount');
        $completed = Withdrawal::where('status', 'completed')->count();
        $completedAmount = Withdrawal::where('status', 'completed')->sum('net_amount');
        $totalFees = Withdrawal::sum('fee');

        return [
            Stat::make('Pending Withdrawals', $pending)
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),


            Stat::make('Processing Withdrawals', $processing)
                ->description('Being processed')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),


            Stat::make('Completed Withdrawals', $completed)
                ->description('Successfully processed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),


            Stat::make('Total Fees', 'UGX ' . number_format($totalFees))
                ->description('Platform fees collected')
                ->color('primary'),
        ];
    }
}
