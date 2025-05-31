<?php

namespace App\Filament\Widgets;

use App\Models\Platform;
use Filament\Widgets\ChartWidget;

class PlatformAnalytics extends ChartWidget
{
    protected static ?string $heading = 'Posts by Platform';

    protected function getData(): array
    {
        $platforms = Platform::withCount('posts')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Posts',
                    'data' => $platforms->pluck('posts_count')->toArray(),
                    'backgroundColor' => [
                        '#1DA1F2', // Twitter
                        '#E1306C', // Instagram
                        '#0077B5', // LinkedIn
                        '#4267B2', // Facebook
                    ],
                ],
            ],
            'labels' => $platforms->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
} 