<?php

namespace App\Filament\Admin\Resources\MedicalRecords;

use App\Filament\Admin\Resources\MedicalRecords\Pages\CreateMedicalRecord;
use App\Filament\Admin\Resources\MedicalRecords\Pages\EditMedicalRecord;
use App\Filament\Admin\Resources\MedicalRecords\Pages\ListMedicalRecords;
use App\Filament\Admin\Resources\MedicalRecords\Schemas\MedicalRecordForm;
use App\Filament\Admin\Resources\MedicalRecords\Tables\MedicalRecordsTable;
use App\Models\MedicalRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MedicalRecordResource extends Resource
{
    protected static ?string $model = MedicalRecord::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static UnitEnum|null|string $navigationGroup = 'Medical Management';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'MedicalRecord';

    public static function form(Schema $schema): Schema
    {
        return MedicalRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicalRecordsTable::configure($table);
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
            'index' => ListMedicalRecords::route('/'),
            'create' => CreateMedicalRecord::route('/create'),
            'edit' => EditMedicalRecord::route('/{record}/edit'),
        ];
    }
}
