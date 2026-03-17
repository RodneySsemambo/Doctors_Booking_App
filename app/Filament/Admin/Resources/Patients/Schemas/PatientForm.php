<?php

namespace App\Filament\Admin\Resources\Patients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        Select::make('user_id')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn($query) =>
                                $query->whereNotNull('name')
                            )
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('first_name')
                            ->required(),
                        TextInput::make('last_name')
                            ->required(),

                        TextInput::make('city')
                            ->required(),

                        TextInput::make('country')
                            ->required(),
                        DatePicker::make('date_of_birth')
                            ->displayFormat('Y-m-d')
                            ->maxDate(now())
                            ->required(),
                        Select::make('gender')
                            ->options(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'])
                            ->required(),
                        Select::make('blood_group')
                            ->options([
                                'A+' => 'A+',
                                'A-' => 'A ',
                                'B+' => 'B+',
                                'B-' => 'B ',
                                'O+' => 'O+',
                                'O-' => 'O ',
                                'AB+' => 'A b+',
                                'AB-' => 'A b ',
                            ])
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('address')
                            ->required()
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('emergency_phone')
                            ->tel()
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('emergency_name')
                            ->required(),
                    ])
                    ->columns(1),
                Section::make('Profile Photo')
                    ->schema([
                        FileUpload::make('profile_photo')
                            ->required()
                            ->image()
                            ->maxSize(1024)
                            ->directory('patients/photos')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Medical Information')
                    ->schema([
                        Textarea::make('medical_history')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull()
                            ->helperText('Brief medical history, chronic conditions, etc.')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('allergies')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText('Known allergies to medications, food, etc.')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
