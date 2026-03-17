<?php

namespace App\Filament\Admin\Resources\MedicalRecords\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MedicalRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('patient_id')
                    ->relationship('patient', 'id')
                    ->required(),
                Select::make('appointment_id')
                    ->relationship('appointment', 'id')
                    ->required(),
                Select::make('record_type')
                    ->options([
            'lab_result' => 'Lab result',
            'consultation_note' => 'Consultation note',
            'vaccination' => 'Vaccination',
            'imaging' => 'Imaging',
        ])
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('file_path')
                    ->default(null),
                TextInput::make('file_type')
                    ->default(null),
                TextInput::make('recorded_by')
                    ->required()
                    ->numeric(),
                DatePicker::make('recorded_date')
                    ->required(),
            ]);
    }
}
