<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Appointment;
use Appointments;
use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LastAppointments extends TableWidget
{
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()
                    ->with(['patient', 'doctor'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('patient.first_name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('doctor.first_name')
                    ->label('Doctor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('appointment_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('appointment_time')
                    ->label('Time')
                    ->dateTime('H:i'),

                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'completed',
                        'danger' => 'cancelled',
                        'secondary' => 'no-show',
                    ]),
                TextColumn::make('created_at')
                    ->label('Booked')
                    ->since()
                    ->sortable()
            ])
            ->heading('Latest Appointments')
            ->description('Recently booked appointments')
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
