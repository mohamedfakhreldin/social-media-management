<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // For logging success/failure

class PublishPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The post instance.
     *
     * @var \App\Models\Post
     */
    protected $post;

    /**
     * Create a new job instance.
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
       
        if ($this->post->status === 'scheduled' && $this->post->scheduled_time <= now()) {
            try {
      
                // For now, we'll just update the status as per your original logic.
                $this->post->update(['status' => 'published']);

                // Log the success
                Log::info("Post ID: {$this->post->id} successfully published.");



            } catch (\Exception $e) {
                // Log any errors that occur during publishing
                Log::error("Failed to publish Post ID: {$this->post->id}. Error: {$e->getMessage()}");

           
            }
        } else {
            Log::info("Post ID: {$this->post->id} is no longer scheduled or not due. Skipping publication.");
        }
    }
}

