<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold">Scheduled Posts</h2>
            </div>

            <div class="grid grid-cols-7 gap-2">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="text-center font-medium">{{ $day }}</div>
                @endforeach

                @php
                    $start = now()->startOfMonth()->startOfWeek();
                    $end = now()->endOfMonth()->endOfWeek();
                    $posts = $this->getPosts();
                @endphp

                @for($date = $start; $date <= $end; $date->addDay())
                    <div class="min-h-[100px] border rounded-lg p-2 {{ $date->isToday() ? 'bg-primary