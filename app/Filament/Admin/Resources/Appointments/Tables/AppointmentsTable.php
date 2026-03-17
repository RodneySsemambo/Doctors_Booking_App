<?php

namespace App\Filament\Admin\Resources\Appointments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('appointment_number')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('doctor.first_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('patient.first_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('hospital.name')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('appointment_date')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->sortable(),
                TextColumn::make('appointment_time')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->sortable(),
                TextColumn::make('appointment_type')
                    ->badge()
                    ->colors([
                        'info' => 'in_person',
                        'primary' => 'video',
                        'warning' => 'phone',
                    ])
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'confirmed',
                        'warning' => 'pending',
                        'danger' => 'cancelled',
                        'secondary' => 'no_show',
                        'primary' => 'compeleted',
                    ])
                    ->sortable()
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'refunded',
                    ])
                    ->sortable()
                    ->searchable(),
                TextColumn::make('consultation_fee')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cancelled_by')
                    ->badge()
                    ->colors([
                        'info' => 'doctor',
                        'success' => 'patient',
                        'warning' => 'system',
                    ])
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cancelled_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reminded_at')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('appointment_type')
                    ->options([
                        'in_person' => 'In person',
                        'video' => 'Video',
                        'phone' => 'Phone',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'confirmed' => 'Confirmed',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No show',
                        'completed' => 'Completed',
                    ]),
                SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                    ]),
                SelectFilter::make('cancelled_by')
                    ->options([
                        'doctor' => 'Doctor',
                        'patient' => 'Patient',
                        'system' => 'System',
                    ]),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make()

            ]);
    }
}
