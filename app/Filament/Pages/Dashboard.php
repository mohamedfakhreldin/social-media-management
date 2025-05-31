<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PostCalendar;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderWidgets(): array
    {
        return [
            PostCalendar::class,
        ];
    }
} 