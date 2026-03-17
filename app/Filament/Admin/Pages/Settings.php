<?php

namespace App\Filament\Admin\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class Settings extends Page implements HasForms

{
    use InteractsWithForms;
    protected string $view = 'filament.admin.pages.settings';
    protected static UnitEnum|null|string $navigationGroup = 'Settings';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 7;

    public ?array $data = [];


    public function mount(): void
    {
        $this->form->fill([
            'app_name' => config('app . name'),
            'app_email' => config('mail.from.address'),
            'app_phone' => setting('app_phone', '075666992'),
            'app_address' => setting('app_address'),
            'currency' => setting('currency', 'UGX'),
            'appointment_duration' => setting('appointment_duration', 30),
            'appointment_buffer' => setting('appointment_buffer', 5),
            'enable_sms' => setting('enable_sms', false),
            'enable_email' => setting('enable_email', true),
            'business_hours_from' => setting('business_hours_from', '08:00'),
            'business_hours_to' => setting('business_hours_to', '17:00'),
            'max_appointments_per_day' => setting('max_appointments_per_day', 50),
            'require_payment' => setting('require_payment', false),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('General Settings')
                    ->schema([
                        TextInput::make('app_name')
                            ->label('Application Name')
                            ->required(),

                        TextInput::make('app_email')
                            ->label('Email Address')
                            ->email()
                            ->required(),

                        TextInput::make('app_phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required(),

                        Textarea::make('app_address')
                            ->label('Address')
                            ->rows(2)
                            ->columnSpanFull(),

                        FileUpload::make('app_logo')
                            ->label('Logo')
                            ->image()
                            ->directory('settings')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Business Settings')
                    ->schema([
                        Select::make('currency')
                            ->options([
                                'UGX' => 'UGX - Ugandan Shilling',
                                'USD' => 'USD - US Dollar',
                                'KES' => 'KES - Kenyan Shilling',
                                'TZS' => 'TZS - Tanzanian Shilling',
                            ])
                            ->required(),

                        TimePicker::make('business_hours_from')
                            ->label('Business Hours From')
                            ->required()
                            ->seconds(false),

                        TimePicker::make('business_hours_to')
                            ->label('Business Hours To')
                            ->required()
                            ->seconds(false),

                        TextInput::make('appointment_duration')
                            ->label('Default Appointment Duration')
                            ->numeric()
                            ->suffix('minutes')
                            ->required()
                            ->minValue(15)
                            ->maxValue(120),

                        TextInput::make('appointment_buffer')
                            ->label('Buffer Time Between Appointments')
                            ->numeric()
                            ->suffix('minutes')
                            ->required()
                            ->minValue(0)
                            ->maxValue(30),

                        TextInput::make('max_appointments_per_day')
                            ->label('Max Appointments Per Day')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                    ])
                    ->columns(2),

                Section::make('Notification Settings')
                    ->schema([
                        Toggle::make('enable_email')
                            ->label('Enable Email Notifications')
                            ->default(true)
                            ->helperText('Send email notifications to patients and doctors'),

                        Toggle::make('enable_sms')
                            ->label('Enable SMS Notifications')
                            ->default(false)
                            ->helperText('Send SMS notifications (requires SMS gateway setup)'),

                        Toggle::make('notify_new_appointment')
                            ->label('Notify on New Appointment')
                            ->default(true),

                        Toggle::make('notify_appointment_reminder')
                            ->label('Send Appointment Reminders')
                            ->default(true)
                            ->helperText('Send reminder 24 hours before appointment'),
                    ])
                    ->columns(2),

                Section::make('Payment Settings')
                    ->schema([
                        Toggle::make('require_payment')
                            ->label('Require Payment Before Appointment')
                            ->default(false),

                        Toggle::make('allow_cash_payment')
                            ->label('Allow Cash Payment')
                            ->default(true),

                        Toggle::make('allow_card_payment')
                            ->label('Allow Card Payment')
                            ->default(true),

                        Toggle::make('allow_mobile_money')
                            ->label('Allow Mobile Money')
                            ->default(true),

                        TextInput::make('tax_rate')
                            ->label('Tax Rate (%)')
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            setting([$key => $value]);
        }

        Notification::make()
            ->success()
            ->title('Settings Saved')
            ->body('Your settings have been saved successfully.')
            ->send();
    }
}
