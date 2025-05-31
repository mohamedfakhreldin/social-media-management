<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\Platform;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PostAnalytics extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPosts = Post::count();
        $publishedPosts = Post::where('status', 'published')->count();
        $scheduledPosts = Post::where('status', 'scheduled')->count();
        
        $successRate = $totalPosts > 0 
            ? round(($publishedPosts / $totalPosts) * 100, 1) 
            : 0;

        return [
            Stat::make('Total Posts', $totalPosts)
                ->description('All time posts')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

            Stat::make('Published Posts', $publishedPosts)
                ->description('Successfully published')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Scheduled Posts', $scheduledPosts)
                ->description('Awaiting publication')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Success Rate', $successRate . '%')
                ->description('Publishing success rate')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
        ];
    }
} 