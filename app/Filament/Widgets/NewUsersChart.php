<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use Carbon\Carbon;

class NewUsersChart extends ChartWidget
{
    protected static ?string $heading = ' Last 6 MonthsNew Users';

    protected function getData(): array
    {
        // Group by month for the last 6 months
        $startDate = Carbon::now()->subMonths(5)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $users = User::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($user) {
                return Carbon::parse($user->created_at)->format('F Y'); // Example: "May 2025"
            });

        $labels = [];
        $data = [];

        // Ensure every month is represented
        for ($date = $startDate->copy(); $date <= $endDate; $date->addMonth()) {
            $monthLabel = $date->format('F Y');
            $labels[] = $monthLabel;
            $data[] = isset($users[$monthLabel]) ? count($users[$monthLabel]) : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)', // Tailwind blue-500
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
