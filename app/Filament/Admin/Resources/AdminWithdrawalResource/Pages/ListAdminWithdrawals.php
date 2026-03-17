<?php

namespace App\Filament\Admin\Resources\AdminWithdrawalResource\Pages;

use App\Filament\Admin\Resources\AdminWithdrawalResource\AdminWithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminWithdrawals extends ListRecords
{
    protected static string $resource = AdminWithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
