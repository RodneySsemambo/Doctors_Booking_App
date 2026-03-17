<?php

namespace App\Filament\Admin\Resources\Payments\Tables;

use App\Models\Payment;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use SebastianBergmann\CodeCoverage\Filter;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payment_number')

                    ->searchable(),
                TextColumn::make('appointment.appointment_type')
                    ->label('Appointment Type')
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'in_person' => 'success',
                        'video' => 'danger',
                        'phone' => 'info',
                        default => 'primary',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('appointment.patient.first_name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('appointment.doctor.first_name')
                    ->label('Doctor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->money('UGX', true)
                    ->sortable(),
                TextColumn::make('currency')

                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone_number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('payment_method')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'mtn_mobile_money' => 'success',
                        'airtel_mobile_money' => 'danger',
                        'flutterwave' => 'info',
                        'cash' => 'warning',
                        default => 'primary',
                    }),

                TextColumn::make('payment_provider')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->sortable()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        default => 'primary',
                    })
                    ->badge(),

                TextColumn::make('transaction_reference')
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->searchable(),
                TextColumn::make('provider_reference')
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->searchable(),
                TextColumn::make('initiated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->sortable(),
                TextColumn::make('refund_amount')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->sortable(),
                TextColumn::make('refunded_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)

                    ->sortable(),
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
                SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ])
                    ->multiple(),
                SelectFilter::make('payment_method')->options([
                    'Mtn' => 'Mtn',
                    'Airtel' => 'Airtel',
                    'bank_transfer' => 'Bank Transfer',
                    'cash' => 'Cash',
                ])
                    ->multiple(),
                SelectFilter::make('payment_date')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date'),
                        DatePicker::make('until')
                            ->label('Until Date'),
                    ])

                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn($query, $date) => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn($query, $date) => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Payment $record) {
                        $record->update(['status' => 'completed', 'pending']);
                        $record->appointment->update(['payment_status' => 'paid']);

                        Notification::make()
                            ->success()
                            ->title('Payment Completed')
                            ->body("The payment of UGX {$record->amount} has been marked as completed.")
                            ->send();
                    }),

                Action::make('refund_amount')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('refund_reason')
                            ->label('Reason for Refund')
                            ->required(),
                    ])
                    ->action(function (Payment $record, array $data) {
                        $record->update([
                            'status' => 'refunded',
                            'notes' => ($record->notes ? $record->notes . "\n\n" : '') .
                                'Refunded: ' . $data['refund_reason']
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Payment Refunded')
                            ->body("The payment of UGX {$record->amount} has been refunded.")
                            ->send();
                    }),

                Action::make('download_invoice')
                    ->label('Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->action(function (Payment $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadView('invoices.payment', ['payment' => $record])
                                ->output();
                        }, "invoice-{$record->id}.pdf");
                    }),

                Action::make('send_receipt')
                    ->label('Send Receipt')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Payment $record) {
                        // Send email with receipt
                        Notification::make()
                            ->success()
                            ->title('Receipt Sent')
                            ->body("The payment receipt has been sent to {$record->appointment->patient->user->email}.")
                            ->send();
                    }),

                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),
            ]);
    }
}
