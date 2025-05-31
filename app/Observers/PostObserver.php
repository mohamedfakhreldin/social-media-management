<?php

namespace App\Observers;

use App\Models\Post;
use App\Models\ActivityLog;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        $user = request()->user()?->id || auth()->id() || $post->user_id;
        ActivityLog::create([
            'user_id' => $user,
            'action' => 'created',
            'description' => "Created new post: {$post->title}",
       
        ]);
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        $changes = $post->getDirty();
        $original = $post->getOriginal();

        $user = request()->user()?->id || auth()->id() || $post->user_id;
        ActivityLog::create([
            'user_id' => $user,
            'action' => 'updated',
            'description' => "Updated post: {$post->title}",
         
        ]);
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        $user = request()->user()?->id || auth()->id() || $post->user_id;
        ActivityLog::create([
            'user_id' => $user,
            'action' => 'deleted',
            'description' => "Deleted post: {$post->title}",
       
        ]);
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        $user = request()->user()?->id || auth()->id() || $post->user_id;
        ActivityLog::create([
            'user_id' => $user,
            'action' => 'restored',
            'description' => "Restored post: {$post->title}",
        ]);
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        $user = request()->user()?->id() || auth()->id() || $post->user_id;
        ActivityLog::create([
            'user_id' => $user,
            'action' => 'force_deleted',
            'description' => "Permanently deleted post: {$post->title}",
        ]);
    }
} 