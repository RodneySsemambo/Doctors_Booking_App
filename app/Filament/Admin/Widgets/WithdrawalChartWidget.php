<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Withdrawal;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class WithdrawalChartWidget extends ChartWidget
{
    protected ?string $heading = 'Withdrawals Trend';
    protected static ?int $sort = 4;
    protected int|array|string $columnSpan = 'md';

    protected function getData(): array
    {
        $data = Trend::model(Withdrawal::class)
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Withdrawals',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
