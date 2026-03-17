<?php

namespace App\Filament\Admin\Resources\WithdrawalResource\Pages;

use App\Filament\Admin\Resources\WithdrawalResource\WithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWithdrawals extends ListRecords
{
    protected static string $resource = WithdrawalResource::class;
}
