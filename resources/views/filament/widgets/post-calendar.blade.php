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
                    <div class="min-h-[100px] border rounded-lg p-2 {{ $date->isToday() ? 'bg-primary-50' : '' }}">
                        <div class="text-sm {{ $date->isToday() ? 'font-bold' : '' }}">
                            {{ $date->format('j') }}
                        </div>
                        
                        @if($posts->has($date->format('Y-m-d')))
                            <div class="mt-2 space-y-1">
                                @foreach($posts[$date->format('Y-m-d')] as $post)
                                    <div class="text-xs p-1 rounded
                                        @if($post->status === 'published') bg-success-100 text-success-700
                                        @elseif($post->status === 'scheduled') bg-warning-100 text-warning-700
                                        @else bg-gray-100 text-gray-700
                                        @endif">
                                        <div class="font-medium truncate">{{ $post->title }}</div>
                                        <div class="text-xs">{{ $post->scheduled_time->format('g:i A') }}</div>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($post->platforms as $platform)
                                                <span class="px-1 py-0.5 text-xs rounded-full bg-gray-200">
                                                    {{ $platform->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget> 