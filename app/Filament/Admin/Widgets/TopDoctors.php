<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Doctor;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopDoctors extends TableWidget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Doctor::query()
                    ->withAvg('reviews', 'rating')
                    ->orderByDesc('rating')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('first_name')
                    ->label('Doctor')
                    ->searchable(),

                // ✅ Specialization relation
                TextColumn::make('specialization.name')
                    ->label('Specialization')
                    ->sortable(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(
                        fn($state) =>
                        $state ? number_format($state, 1) . ' ⭐' : 'No ratings'
                    )
                    ->sortable(),
            ])
            ->heading('Top Rated Doctors')
            ->description('Doctors with the highest average ratings');
    }
}
