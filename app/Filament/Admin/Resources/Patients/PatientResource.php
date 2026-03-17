<?php

namespace App\Filament\Admin\Resources\Patients;

use App\Filament\Admin\Resources\Patients\Pages\CreatePatient;
use App\Filament\Admin\Resources\Patients\Pages\EditPatient;
use App\Filament\Admin\Resources\Patients\Pages\ListPatients;
use App\Filament\Admin\Resources\Patients\RelationManagers\AppointmentsRelationManager;
use App\Filament\Admin\Resources\Patients\RelationManagers\PaymentRelationManager;
use App\Filament\Admin\Resources\Patients\Schemas\PatientForm;
use App\Filament\Admin\Resources\Patients\Tables\PatientsTable;
use App\Models\Patient;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static UnitEnum|null|string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 4;


    protected static ?string $recordTitleAttribute = 'Patient';

    public static function form(Schema $schema): Schema
    {
        return PatientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PatientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AppointmentsRelationManager::class,
            PaymentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatients::route('/'),
            'create' => CreatePatient::route('/create'),
            'edit' => EditPatient::route('/{record}/edit'),
        ];
    }
}
