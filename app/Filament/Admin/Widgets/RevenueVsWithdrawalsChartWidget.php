<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Payment;
use App\Models\Withdrawal;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RevenueVsWithdrawalsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Revenue vs Withdrawals';
    protected static ?int $sort = 5;
    protected int|array|string $columnSpan = 'md';

    protected function getData(): array
    {
        $revenueData = Trend::model(Payment::class)
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->sum('amount');

        $withdrawalData = Trend::model(Withdrawal::class)
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->sum('net_amount');

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $revenueData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Withdrawals',
                    'data' => $withdrawalData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $revenueData->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
