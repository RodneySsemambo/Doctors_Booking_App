<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Monthly Revenue Overview';
    protected static ?int $sort = 3;
    protected int|array|string $columnSpan = 'md';


    protected function getData(): array
    {
        $data = Payment::where('status', 'completed')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->whereYear('created_at', now())
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $months = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'
        ];

        $revenues = array_fill(0, 12, 0);

        foreach ($data as $item) {
            $revenues[$item->month - 1] = $item->total;
        }
        return [
            'datasets' => [
                [
                    'label' => 'Revenue (UGX)',
                    'data' => $revenues,
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
