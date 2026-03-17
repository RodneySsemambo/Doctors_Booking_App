<?php

namespace App\Filament\Admin\Resources\AdminWithdrawalResource\Pages;

use App\Filament\Admin\Resources\AdminWithdrawalResource\AdminWithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminWithdrawal extends EditRecord
{
    protected static string $resource = AdminWithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
