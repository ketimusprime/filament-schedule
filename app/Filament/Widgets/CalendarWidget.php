<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ActivityScheduleResource;
use App\Models\ActivitySchedule;
use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    // protected static string $view = 'filament.widgets.calendar-widget';

    public function fetchEvents(array $fetchInfo): array
    {
        return ActivitySchedule::query()
            ->where('activity_date', '>=', $fetchInfo['start'])
            ->where('activity_date', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (ActivitySchedule $event) => EventData::make()
                    ->id($event->id)
                    ->title($event->category->name . ' - ' . $event->subcategory->name)
                    ->start($event->activity_date)
                    ->end($event->activity_date)
                    ->url(
                        url: ActivityScheduleResource::getUrl(name: 'edit', parameters: ['record' => $event]),
                        shouldOpenUrlInNewTab: false
                    )
            )
            ->toArray();
    }
}
