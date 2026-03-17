<?php

namespace App\Filament\Admin\Resources\Specializations;

use App\Filament\Admin\Resources\Specializations\Pages\CreateSpecialization;
use App\Filament\Admin\Resources\Specializations\Pages\EditSpecialization;
use App\Filament\Admin\Resources\Specializations\Pages\ListSpecializations;
use App\Filament\Admin\Resources\Specializations\Schemas\SpecializationForm;
use App\Filament\Admin\Resources\Specializations\Tables\SpecializationsTable;
use App\Models\Specialization;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\In;
use PhpParser\Node\Scalar\String_;
use UnitEnum;

class SpecializationResource extends Resource
{
    protected static ?string $model = Specialization::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static UnitEnum|string|null $navigationGroup = 'Medical Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'specialization';

    public static function form(Schema $schema): Schema
    {
        return SpecializationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpecializationsTable::configure($table);
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
            'index' => ListSpecializations::route('/'),
            'create' => CreateSpecialization::route('/create'),
            'edit' => EditSpecialization::route('/{record}/edit'),
        ];
    }
}
