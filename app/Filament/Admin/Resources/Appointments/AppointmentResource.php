<?php

namespace App\Filament\Admin\Resources\Appointments;

use App\Filament\Admin\Resources\Appointments\Pages\CreateAppointment;
use App\Filament\Admin\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Admin\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Admin\Resources\Appointments\Schemas\AppointmentForm;
use App\Filament\Admin\Resources\Appointments\Tables\AppointmentsTable;
use App\Models\Appointment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static UnitEnum|null|string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'Appointment';

    public static function form(Schema $schema): Schema
    {
        return AppointmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppointmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointments::route('/'),
            'create' => CreateAppointment::route('/create'),
            'edit' => EditAppointment::route('/{record}/edit'),
        ];
    }
}
