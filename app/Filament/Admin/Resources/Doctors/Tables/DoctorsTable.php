<?php

namespace App\Filament\Admin\Resources\Doctors\Tables;

use App\Models\Doctor;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

use function Laravel\Prompts\select;

class DoctorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('specialization.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('license_number')
                    ->searchable(),
                TextColumn::make('years_of_experience')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('profile_photo')
                    ->searchable(),

                TextColumn::make('consultation_fee')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_reviews')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('hospital_affiliation')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('video_consultation_available')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),


                IconColumn::make('is_verified')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_available')
                    ->boolean()
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
                SelectFilter::make('specialization')
                    ->relationship('specialization', 'name')
                    ->multiple()
                    ->preload(),
                TernaryFilter::make('is_verified')
                    ->label('Verification Status')
                    ->placeholder('All Doctors')
                    ->trueLabel('Verified only')
                    ->falseLabel('Unverified only'),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)

            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('verify')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(Doctor $record) => $record->update(['is_verified' => true]))
                    ->visible(fn(Doctor $record) => !$record->is_verified),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
