<?php

namespace App\Filament\Admin\Resources\Patients\RelationManagers;

use App\Filament\Admin\Resources\Payments\PaymentResource;
use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'payment';

    protected static ?string $relatedResource = PaymentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')
                    ->money('ugx', true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('initiated_at')
                    ->label('payment_date')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('payment_method')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->sortable()
                    ->searchable(),
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
            ->bulkActions(
                [
                    BulkActionGroup::make([
                        DeleteBulkAction::make(),
                    ]),
                ],
            );
    }
}
