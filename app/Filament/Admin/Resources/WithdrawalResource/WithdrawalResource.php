<?php

namespace App\Filament\Admin\Resources\WithdrawalResource;

use App\Filament\Admin\Resources\WithdrawalResource\Pages;
use App\Filament\Admin\Resources\WithdrawalResource\Pages\EditWithdrawal;
use App\Filament\Admin\Resources\WithdrawalResource\Pages\ListWithdrawals;
use App\Filament\Admin\Resources\WithdrawalResource\Pages\ViewWithdrawal;
use App\Models\Withdrawal;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static string|UnitEnum|null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('withdrawal_number')->label('Withdrawal Number')->disabled(),
                Select::make('doctor_id')->relationship('doctor', 'first_name')->disabled(),
                TextInput::make('amount')->label('Amount')->disabled(),
                TextInput::make('fee')->label('Fee')->disabled(),
                TextInput::make('net_amount')->label('Net Amount')->disabled(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                    ]),
                Select::make('method')
                    ->options([
                        'mobile_money' => 'Mobile Money',
                        'bank_transfer' => 'Bank Transfer',
                        'paypal' => 'PayPal',
                        'cash' => 'Cash',
                    ]),
                Textarea::make('notes')->label('Notes'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('withdrawal_number')->label('ID')->searchable(),
                TextColumn::make('doctor.first_name')->label('Doctor')->formatStateUsing(fn($record) => ($record->doctor->first_name ?? '') . ' ' . ($record->doctor->last_name ?? ''))->searchable(),
                TextColumn::make('amount')->label('Amount')->formatStateUsing(fn($state) => 'UGX ' . number_format($state))->sortable(),
                TextColumn::make('fee')->label('Fee')->formatStateUsing(fn($state) => 'UGX ' . number_format($state)),
                TextColumn::make('net_amount')->label('Net')->formatStateUsing(fn($state) => 'UGX ' . number_format($state))->sortable(),
                BadgeColumn::make('method')->label('Method')->formatStateUsing(fn($state) => ucwords(str_replace('_', ' ', $state)))
                    ->colors([
                        'blue' => 'bank_transfer',
                        'green' => 'mobile_money',
                        'purple' => 'paypal',
                        'gray' => 'cash',
                    ]),
                BadgeColumn::make('status')->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'gray' => 'cancelled',
                    ]),
                TextColumn::make('requested_at')->label('Date')->dateTime('M d, Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('doctor_id')->relationship('doctor', 'first_name')->label('Doctor'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('mark_processing')->label('Start Processing')->icon('heroicon-o-arrow-right')->color('info')->visible(fn($record) => $record->status === 'pending')->requiresConfirmation()->action(fn($record) => $record->update(['status' => 'processing', 'processed_at' => now()])),
                Action::make('complete')->label('Complete')->icon('heroicon-o-check')->color('success')->visible(fn($record) => in_array($record->status, ['pending', 'processing']))->requiresConfirmation()->action(fn($record) => $record->update(['status' => 'completed', 'processed_at' => now()])),
                Action::make('cancel')->label('Cancel')->icon('heroicon-o-x-mark')->color('danger')->visible(fn($record) => in_array($record->status, ['pending', 'processing']))->requiresConfirmation()->action(fn($record) => $record->update(['status' => 'cancelled'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_processing')->label('Mark as Processing')->color('info')->requiresConfirmation()->action(fn($records) => $records->each(fn($r) => $r->update(['status' => 'processing', 'processed_at' => now()]))),
                    BulkAction::make('mark_completed')->label('Mark as Completed')->color('success')->requiresConfirmation()->action(fn($records) => $records->each(fn($r) => $r->update(['status' => 'completed', 'processed_at' => now()]))),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWithdrawals::route('/'),
            'view' => ViewWithdrawal::route('/{record}'),
            'edit' => EditWithdrawal::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }
}
