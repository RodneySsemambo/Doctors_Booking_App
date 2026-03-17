<?php

namespace App\Filament\Admin\Resources\Reviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doctor.first_name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('patient.first_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('appointment.appointment_type')
                    ->label('Appointment_type')
                    ->sortable(),
                TextColumn::make('rating')
                    ->numeric()
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state))

                    ->sortable(),
                IconColumn::make('recommend')
                    ->boolean(),
                IconColumn::make('is_verified')
                    ->boolean(),
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
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
