<?php

namespace App\Filament\Admin\Resources\Hospitals\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HospitalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([

                        TextInput::make('name')
                            ->required(),
                        TextInput::make('phone')
                            ->tel()
                            ->required(),
                        TextInput::make('rating')
                            ->required()
                            ->numeric(),
                        Toggle::make('is_active')
                            ->required(),

                    ])
                    ->columns(2),
                Section::make('Address')
                    ->schema([

                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        TextInput::make('address')
                            ->required(),
                        TextInput::make('city')
                            ->required(),
                        TextInput::make('country')
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Facilities & Operations')
                    ->schema([

                        TextInput::make('latitude')
                            ->required()
                            ->numeric(),
                        TextInput::make('longtitude')
                            ->required()
                            ->numeric(),
                        Textarea::make('openning_hours')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('facilities')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),



            ]);
    }
}
