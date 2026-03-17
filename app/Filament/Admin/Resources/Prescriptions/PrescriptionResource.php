<?php

namespace App\Filament\Admin\Resources\Prescriptions;

use App\Filament\Admin\Resources\Prescriptions\Pages\CreatePrescription;
use App\Filament\Admin\Resources\Prescriptions\Pages\EditPrescription;
use App\Filament\Admin\Resources\Prescriptions\Pages\ListPrescriptions;
use App\Filament\Admin\Resources\Prescriptions\Schemas\PrescriptionForm;
use App\Filament\Admin\Resources\Prescriptions\Tables\PrescriptionsTable;
use App\Models\Prescription;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?string $recordTitleAttribute = 'Prescription';

    protected static UnitEnum|string|null $navigationGroup = 'Medical Management';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PrescriptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrescriptionsTable::configure($table);
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
            'index' => ListPrescriptions::route('/'),
            'create' => CreatePrescription::route('/create'),
            'edit' => EditPrescription::route('/{record}/edit'),
        ];
    }
}
