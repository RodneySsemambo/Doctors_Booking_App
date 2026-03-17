<?php

namespace App\Filament\Admin\Resources\Doctors;

use App\Filament\Admin\Resources\Doctors\Pages\CreateDoctor;
use App\Filament\Admin\Resources\Doctors\Pages\EditDoctor;
use App\Filament\Admin\Resources\Doctors\Pages\ListDoctors;
use App\Filament\Admin\Resources\Doctors\Schemas\DoctorForm;
use App\Filament\Admin\Resources\Doctors\Tables\DoctorsTable;
use App\Models\Doctor;
use BackedEnum;
use Carbon\Unit;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Enum;
use UnitEnum;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;
    protected static UnitEnum|null|string $navigationGroup = 'Medical Management';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 1;


    //protected static string|UnitEnum|null $navigationGroup = 'User Management';

    protected static ?string $recordTitleAttribute = 'doctor';

    public static function form(Schema $schema): Schema
    {
        return DoctorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DoctorsTable::configure($table);
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
            'index' => ListDoctors::route('/'),
            'create' => CreateDoctor::route('/create'),
            'edit' => EditDoctor::route('/{record}/edit'),
        ];
    }
}
