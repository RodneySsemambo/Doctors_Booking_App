<?php

namespace App\Filament\Admin\Resources\Hospitals\Pages;

use App\Filament\Admin\Resources\Hospitals\HospitalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHospital extends EditRecord
{
    protected static string $resource = HospitalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
