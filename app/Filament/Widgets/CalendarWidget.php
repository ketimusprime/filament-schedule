<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ActivityScheduleResource;
use App\Models\ActivitySchedule;
use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public function fetchEvents(array $fetchInfo): array
{
    // Peta warna berdasarkan kategori
    $categoryColors = [
        'STUDIO' => '#10b981',  // Hijau
        'EVENT' => '#1e40af',  // Biru
        'LIPUTAN' => '#f59e0b',  // Kuning
        'OUTDOOR' => '#ef4444',  // Merah
    ];

    // Peta warna berdasarkan subkategori
    $subcategoryColors = [
        'SCHOOL MINI STUDIO' => '#f59e0b',  // Kuning
        'Subkategori 2' => '#8b5cf6',  // Ungu
        'Subkategori 3' => '#fb923c',  // Oranye
    ];

    return ActivitySchedule::query()
        ->where('activity_date', '>=', $fetchInfo['start'])
        ->where('activity_date', '<=', $fetchInfo['end'])
        ->get()
        ->map(function (ActivitySchedule $event) use ($categoryColors, $subcategoryColors) {
            return EventData::make()
                ->id($event->id)
                ->title($event->category->name . ' - ' . $event->subcategory->name)
                ->start($event->activity_date)
                ->end($event->activity_date)
                ->url(
                    url: ActivityScheduleResource::getUrl(name: 'edit', parameters: ['record' => $event]),
                    shouldOpenUrlInNewTab: false
                )
                // Mengatur background color berdasarkan kategori atau subkategori
                ->backgroundColor(
                    $categoryColors[$event->category->name] ?? '#d1d5db'  // Default warna jika kategori tidak ada
                )
                ->borderColor(
                    $categoryColors[$event->category->name] ?? '#d1d5db'  // Border warna sesuai kategori
                )
                ->textColor('#ffffff'); // Menetapkan warna teks ke putih untuk kontras yang lebih baik
        })
        ->toArray();
}

}
