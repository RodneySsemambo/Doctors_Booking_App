<?php

namespace App\Filament\Admin\Resources\Patients\RelationManagers;

use App\Filament\Admin\Resources\Patients\PatientResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AppointmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'appointments';
    protected static ?string $recordTitleAttribute = 'Appointments';

    protected static ?string $relatedResource = PatientResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doctor.first_name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('appointment_date')->date(),
                TextColumn::make('appointment_time')->time('H:i'),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'confirmed',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
