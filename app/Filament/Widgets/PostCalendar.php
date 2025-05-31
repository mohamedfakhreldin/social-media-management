<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Guava\Calendar\Widgets\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;

class PostCalendar extends CalendarWidget
{
    protected bool $eventClickEnabled = true;
    protected ?string $defaultEventClickAction = 'view';
    protected string $calendarView = 'dayGridMonth';
    public function onEventClick(array $info = [], ?string $action = null): void
    {
        // do something on click
        // $info contains the event data:
        // $info['event'] - the event object
     $info['view'] ;
    }
    public function getPosts()
    {
        return Post::query()
            ->where('scheduled_time', '>=', now())
            ->get()->map(function ($post) {
                return CalendarEvent::make()
                    ->title($post->title)
                    ->start($post->scheduled_time)
                    ->end($post->scheduled_time);
            })->toArray();
    }

 
public function getEvents(array $fetchInfo = []):Collection|array
    {

        $posts = Post::query()
        ->where('scheduled_time', '>=', now())
        ->get()->map(function ($post) {
            return CalendarEvent::make()
                ->title($post->title)
                ->start($post->scheduled_time)
                ->end($post->scheduled_time);
        })->toArray();
        
        return $posts;
    }
}