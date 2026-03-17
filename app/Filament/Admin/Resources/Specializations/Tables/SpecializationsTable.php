<?php

namespace App\Filament\Admin\Resources\Specializations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SpecializationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->sortable(),
                IconColumn::make('is_active')
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
                SelectFilter::make('name')
                    ->label('Name of specialization')
                    ->options([
                        'cardiology' => 'cardiology',
                        'cardiologist' => 'cardiologist',
                        'pediatrics' => 'pediatrics',
                        'pediatrician' => 'pediatrician',
                        'orthopedic' => 'orthopedic',
                        'dermatology' => 'dermatology',
                        'dermatologist' => 'dermatologist',
                        'neurology' => 'nurology',
                        'neurologist' => 'nuerologist',
                        'gynecology' => 'gynecology',
                        'gynecologist' => 'gynecologist',
                        'dentist' => 'dentist',
                        'psychiatrist' => 'psychiatrist',
                        'ophthalmologist' => 'ophthalmologist'
                    ])
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
