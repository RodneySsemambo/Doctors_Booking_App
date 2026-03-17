<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Appointment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AppointmentsChart extends ChartWidget
{
    protected  ?string $heading = 'Appointments Trend';
    protected static ?int $sort = 2;
    protected int|array|string $columnSpan = 'md';

    protected function getFormSchema(): array
    {
        return [
            SelectFilter::make('date_from')
                ->label('From')
                ->default(now()->subDays(30))
                ->maxDate(fn($get) => $get('date_to') ?: now()),

            SelectFilter::make('date_to')
                ->label('To')
                ->default(now())
                ->minDate(fn($get) => $get('date_from'))
                ->maxDate(now()),
        ];
    }

    protected function getData(): array
    {

        $dateFrom = $this->filterFormData['date_from'] ?? now()->subDays(30);
        $dateTo = $this->filterFormData['date_to'] ?? now();

        $data = Trend::model(Appointment::class)
            ->between(
                start: $dateFrom,
                end: $dateTo,
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Appointments',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
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

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
