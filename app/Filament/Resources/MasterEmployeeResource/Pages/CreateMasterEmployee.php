<?php

namespace App\Filament\Resources\MasterEmployeeResource\Pages;

use App\Filament\Resources\MasterEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMasterEmployee extends CreateRecord
{
    protected static string $resource = MasterEmployeeResource::class;
}
