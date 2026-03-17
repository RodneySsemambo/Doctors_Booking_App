<?php

namespace App\Filament\Admin\Resources\Reviews\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

use function Symfony\Component\Clock\now;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Review Information')
                    ->schema([

                        Select::make('doctor_id')
                            ->relationship('doctor', 'first_name')
                            ->required(),
                        Select::make('patient_id')
                            ->relationship('patient', 'first_name')
                            ->required(),
                        Select::make('appointment_id')
                            ->relationship('appointment', 'appointment_type')
                            ->required(),
                        Select::make('rating')
                            ->options([
                                1 => '⭐ 1 - Poor',
                                2 => '⭐⭐ 2 - Fair',
                                3 => '⭐⭐⭐ 3 - Good',
                                4 => '⭐⭐⭐⭐ 4 - Very Good',
                                5 => '⭐⭐⭐⭐⭐ 5 - Excellent',
                            ])
                            ->required()
                            ->native(false),


                    ])
                    ->columns(2),
                Section::make('Review Content')
                    ->schema([

                        Textarea::make('review_text')
                            ->required()
                            ->columnSpanFull(),
                        Toggle::make('recommend')
                            ->required(),
                        Toggle::make('is_verified')
                            ->required(),
                    ])
                    ->columns(2)



            ]);
    }
}
