<?php

namespace App\Filament\Admin\Resources\Patients\Tables;

use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\View;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PatientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('profile_photo')
                    ->searchable(),
                TextColumn::make('city')
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->searchable(),
                TextColumn::make('address')
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->searchable(),
                TextColumn::make('country')
                    ->searchable(),
                TextColumn::make('date_of_birth')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->sortable(),
                TextColumn::make('gender')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'success',
                        'other' => 'warning'
                    }),
                TextColumn::make('blood_group')
                    ->badge()
                    ->color('danger')
                    ->sortable(),
                TextColumn::make('appointments_count')
                    ->counts('appointments')
                    ->label('Appointments')
                    ->sortable(),
                TextColumn::make('emergency_phone')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('emergency_name')
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other'
                    ]),
                SelectFilter::make('blood_group')
                    ->options([
                        'A+' => 'A+',
                        'A-' => 'A-',
                        'B+' => 'B+',
                        'B-' => 'B-',
                        'AB+' => 'AB+',
                        'AB-' => 'AB-',
                        'O+' => 'O+',
                        'O-' => 'O-'
                    ])
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make()
            ]);
    }
}
