<?php

namespace App\Filament\Admin\Resources\Doctors\Schemas;

use Dom\Text;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DoctorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('user_id')
                            ->hidden()
                            ->default(fn() => null),
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('profile_photo')
                            ->image()
                            ->directory('doctors/photos')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Professional Information')
                    ->schema([
                        TextInput::make('license_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Select::make('specialization_id')
                            ->label('Specialization')
                            ->relationship('specialization', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->maxLength(500),
                            ]),

                        Select::make('hospital_id')
                            ->label('Hospital/Clinic')
                            ->multiple()
                            ->relationship('hospital', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('years_of_experience')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(70)
                            ->suffix('years'),

                        TextInput::make('consultation_fee')
                            ->numeric()
                            ->required()
                            ->prefix('UGX')
                            ->minValue(0),

                        Toggle::make('video_consultation_available')
                            ->label('Video Consultation Available')
                            ->default(false),
                        TextInput::make('languages_spoken')
                            ->label('Languages Spoken')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('total_reviews')
                            ->label('Total Reviews')
                            ->numeric()
                            ->required()
                            ->minValue(0),

                        TextInput::make('rating')
                            ->label('Average Rating')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.1),
                        Textarea::make('bio')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Availability')
                    ->schema([
                        Toggle::make('is_available')
                            ->label(' doctor availablefor appointments')
                            ->required(),

                        TimePicker::make('available_from')

                            ->seconds(false),

                        TimePicker::make('available_to')

                            ->seconds(false)
                            ->after('available_from'),


                    ])
                    ->columns(2),

                Section::make('verification')
                    ->schema([


                        Toggle::make('is_verified')
                            ->label('Verify Doctor')
                            ->default(false)
                            ->helperText('Verified doctors can accept appointments'),
                    ])
                    ->columns(2),
            ]);
    }
}
