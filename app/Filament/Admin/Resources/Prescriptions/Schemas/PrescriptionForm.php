<?php

namespace App\Filament\Admin\Resources\Prescriptions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrescriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Prescription Details')
                    ->schema([
                        Select::make('appointment_id')
                            ->relationship('appointment', 'appointment_type')
                            ->label('Appointment_type')
                            ->required(),
                        Select::make('doctor_id')
                            ->relationship('doctor', 'first_name')
                            ->label('Doctor')
                            ->required(),
                        Select::make('patient_id')
                            ->relationship('patient', 'first_name')
                            ->label('Patient')
                            ->required()
                    ])
                    ->columnSpanFull(),
                Section::make('Medications')
                    ->schema([

                        TextInput::make('prescription_number')
                            ->required(),
                        Textarea::make('medications')
                            ->required()
                            ->columnSpanFull(),


                        Textarea::make('instructions')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('diagnosis')
                            ->required()
                            ->columnSpanFull(),
                        DatePicker::make('valid_until')
                            ->required(),
                        Toggle::make('is_dispensed')
                            ->required(),
                        DateTimePicker::make('dispensed_at'),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
