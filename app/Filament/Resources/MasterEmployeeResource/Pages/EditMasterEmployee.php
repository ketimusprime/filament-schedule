<?php

namespace App\Filament\Resources\MasterEmployeeResource\Pages;

use App\Filament\Resources\MasterEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasterEmployee extends EditRecord
{
    protected static string $resource = MasterEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
