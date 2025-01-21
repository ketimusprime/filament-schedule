<?php

namespace App\Filament\Resources\ActivityScheduleResource\Pages;

use App\Filament\Resources\ActivityScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivitySchedules extends ListRecords
{
    protected static string $resource = ActivityScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
