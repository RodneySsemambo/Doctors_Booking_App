<?php

namespace App\Filament\Admin\Resources\AdminWithdrawalResource;

use App\Filament\Admin\Resources\AdminWithdrawalResource\Pages;
use App\Filament\Admin\Resources\AdminWithdrawalResource\Pages\EditAdminWithdrawal;
use App\Filament\Admin\Resources\AdminWithdrawalResource\Pages\ListAdminWithdrawals;
use App\Filament\Admin\Resources\AdminWithdrawalResource\Pages\ViewAdminWithdrawal;
use App\Models\AdminWithdrawal;
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

class AdminWithdrawalResource extends Resource
{
    protected static ?string $model = AdminWithdrawal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static string|UnitEnum|null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('withdrawal_number')->label('Withdrawal Number')->disabled(),
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
                    ]),
                Textarea::make('method_details')->label('Payment Details'),
                TextInput::make('transaction_id')->label('Transaction ID'),
                Textarea::make('notes')->label('Notes'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('withdrawal_number')->label('ID')->searchable(),
                TextColumn::make('amount')->label('Amount')->formatStateUsing(fn($state) => 'UGX ' . number_format($state))->sortable(),
                TextColumn::make('fee')->label('Fee')->formatStateUsing(fn($state) => 'UGX ' . number_format($state)),
                TextColumn::make('net_amount')->label('Net')->formatStateUsing(fn($state) => 'UGX ' . number_format($state))->sortable(),
                BadgeColumn::make('method')->label('Method')->formatStateUsing(fn($state) => ucwords(str_replace('_', ' ', $state)))
                    ->colors([
                        'blue' => 'bank_transfer',
                        'green' => 'mobile_money',
                        'purple' => 'paypal',
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
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('process')->label('Process')->icon('heroicon-o-check')->color('success')->visible(fn($record) => in_array($record->status, ['pending', 'processing']))->requiresConfirmation()->action(fn($record) => $record->update(['status' => 'completed', 'processed_at' => now()])),
                Action::make('cancel')->label('Cancel')->icon('heroicon-o-x-mark')->color('danger')->visible(fn($record) => in_array($record->status, ['pending', 'processing']))->requiresConfirmation()->action(fn($record) => $record->update(['status' => 'cancelled'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_completed')->label('Mark as Completed')->color('success')->requiresConfirmation()->action(fn($records) => $records->each(fn($r) => $r->update(['status' => 'completed', 'processed_at' => now()]))),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminWithdrawals::route('/'),
            'view' => ViewAdminWithdrawal::route('/{record}'),
            'edit' => EditAdminWithdrawal::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }
}
