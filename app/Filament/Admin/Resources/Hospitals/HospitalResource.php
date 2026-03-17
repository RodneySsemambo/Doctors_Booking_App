<?php

namespace App\Filament\Admin\Resources\Hospitals;

use App\Filament\Admin\Resources\Hospitals\Pages\CreateHospital;
use App\Filament\Admin\Resources\Hospitals\Pages\EditHospital;
use App\Filament\Admin\Resources\Hospitals\Pages\ListHospitals;
use App\Filament\Admin\Resources\Hospitals\Schemas\HospitalForm;
use App\Filament\Admin\Resources\Hospitals\Tables\HospitalsTable;
use App\Models\Hospital;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HospitalResource extends Resource
{
    protected static ?string $model = Hospital::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static UnitEnum|string|null $navigationGroup = 'Facilities';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'Hospital';

    public static function form(Schema $schema): Schema
    {
        return HospitalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HospitalsTable::configure($table);
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
            'index' => ListHospitals::route('/'),
            'create' => CreateHospital::route('/create'),
            'edit' => EditHospital::route('/{record}/edit'),
        ];
    }
}
