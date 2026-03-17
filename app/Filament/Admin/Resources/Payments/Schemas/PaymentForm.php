<?php

namespace App\Filament\Admin\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Details')
                    ->schema([
                        Select::make('appointment_id')
                            ->relationship('appointment', 'appointment_number')
                            ->required()
                            ->getOptionLabelFromRecordUsing(
                                fn($record) =>
                                "#{$record->id} - {$record->patient->name} with Dr. {$record->doctor->first_name}"
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $appointment = \App\Models\Appointment::find($state);
                                    $set('amount', $appointment->consultation_fee ?? 0);
                                    $set('patient_name', $appointment->patient->name ?? '');
                                    $set('doctor_name', $appointment->doctor->name ?? '');
                                }
                            }),


                        TextInput::make('payment_number')
                            ->required(),

                        Select::make('patient_id')
                            ->relationship('patient', 'first_name')
                            ->required(),
                        TextInput::make('amount')
                            ->required()
                            ->numeric(),
                        Select::make('currency')
                            ->options(['UGX' => 'U g x', 'USD' => 'U s d'])
                            ->default('UGX')
                            ->required(),
                        TextInput::make('phone_number')
                            ->tel()
                            ->default(null),
                        Select::make('payment_method')
                            ->options([
                                'mtn_mobile_money' => 'Mtn mobile money',
                                'airtel_mobile_money' => 'Airtel mobile money',
                                'card' => 'Card',
                                'cash' => 'Cash',
                                'flutterwave' => 'Flutterwave',
                            ])
                            ->required(),
                        Select::make('payment_provider')
                            ->options([
                                'mtn' => 'Mtn',
                                'airtel' => 'Airtel',
                                'flutterwave' => 'Flutterwave',
                                'cash' => 'Cash',
                                'stripe' => 'Stripe',
                            ])
                            ->required(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'refunded' => 'Refunded',
                                'processing' => 'Processing',
                                'compeleted' => 'Compeleted',
                                'failed' => 'Failed',
                            ])
                            ->required(),
                        TextInput::make('transaction_reference')
                            ->required(),
                        TextInput::make('provider_reference')
                            ->default(null),
                        Textarea::make('provider_response')
                            ->default(null)
                            ->columnSpanFull(),
                        DateTimePicker::make('initiated_at')
                            ->required(),
                        DateTimePicker::make('completed_at'),
                        Textarea::make('failed_reason')
                            ->default(null)
                            ->columnSpanFull(),
                        TextInput::make('refund_amount')
                            ->numeric()
                            ->default(null),
                        DateTimePicker::make('refunded_at'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
