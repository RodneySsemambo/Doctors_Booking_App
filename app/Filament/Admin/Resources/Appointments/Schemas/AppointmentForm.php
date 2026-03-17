<?php

namespace App\Filament\Admin\Resources\Appointments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('appointment_number')
                    ->required(),
                Select::make('doctor_id')
                    ->relationship('doctor', 'id')
                    ->required(),
                Select::make('patient_id')
                    ->relationship('patient', 'id')
                    ->required(),
                Select::make('hospital_id')
                    ->relationship('hospital', 'name')
                    ->required(),
                DatePicker::make('appointment_date')
                    ->required(),
                DateTimePicker::make('appointment_time')
                    ->required(),
                Select::make('appointment_type')
                    ->options(['in_person' => 'In person', 'video' => 'Video', 'phone' => 'Phone'])
                    ->required(),
                Select::make('status')
                    ->options([
                        'confirmed' => 'Confirmed',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No show',
                        'compeleted' => 'Compeleted',
                    ])
                    ->required(),
                Textarea::make('reason_for_visit')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('symptoms')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('payment_status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'refunded' => 'Refunded'])
                    ->default('pending')
                    ->required(),
                TextInput::make('consultation_fee')
                    ->required()
                    ->numeric(),
                Select::make('cancelled_by')
                    ->options(['doctor' => 'Doctor', 'patient' => 'Patient', 'system' => 'System'])
                    ->default(null),
                Textarea::make('cancellation_reason')
                    ->default(null)
                    ->columnSpanFull(),
                DatePicker::make('cancelled_at'),
                DatePicker::make('reminded_at'),
                DatePicker::make('completed_at'),
            ]);
    }
}
