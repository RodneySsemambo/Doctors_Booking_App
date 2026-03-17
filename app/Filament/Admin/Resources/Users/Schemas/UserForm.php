<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->maxLength(255)
                            ->required(),
                        Select::make('user_type')
                            ->options(['patient' => 'Patient', 'doctor' => 'Doctor', 'admin' => 'Admin', 'user' => 'User'])
                            ->default('user')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->helperText('Leave blank to keep current password')
                            ->required(),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(10)
                            ->required(),

                    ])
                    ->columns(2),
                Section::make('Status Information')
                    ->schema([

                        Toggle::make('is_active')
                            ->required(),
                        DateTimePicker::make('last_login_at'),
                        DateTimePicker::make('phone_verified_at'),
                        DateTimePicker::make('email_verified_at'),
                    ])
                    ->columns(2),
                Section::make('Roles')
                    ->schema([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(1),

            ]);
    }
}
