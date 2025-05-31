<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Models\Post;
use App\Filament\Resources\PostResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static array $middleware = [
        'web',
        'auth',
        'permission:access_custom_page', // Example: Spatie permissions middleware
    ];

    protected static string $resource = PostResource::class;
    protected function beforeFill(): void
    {    Notification::make()
        ->title('Saved successfully!')
        ->danger()
        ->send();

        $user = auth()->user();
        $todayPosts = Post::where('user_id', $user->id)

            ->whereDate('created_at', today())
                  ->whereColumn('scheduled_time', '>', 'created_at')
            ->count();

        if ($todayPosts >= 10) {
         
               $this->redirect(PostResource::getUrl('index'));
             }
         }
    protected function beforeCreate(): void
    {
        // Runs before the form fields are saved to the database.
        $user = auth()->user();
        $todayPosts = Post::where('user_id', $user->id)

            ->whereDate('created_at', today())
            ->count();

        if ($todayPosts >= 2) {
         
        }
    }
    public static function getMiddleware(): array
    {
        return [
        //    'post_count', // Example: Spatie permissions middleware
        ];
    }
    public static function middleware(array $middleware): array
    {
        return array_merge($middleware, [
            // Add middleware that applies to all pages/actions of this resource
            'post_count', // Custom middleware
        ]);
    }


} 