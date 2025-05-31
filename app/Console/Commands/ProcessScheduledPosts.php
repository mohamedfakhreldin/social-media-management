<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Post;
use Illuminate\Console\Command;
use App\Jobs\PublishPostJob; // Import the new job

class ProcessScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:process-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds and dispatches jobs for scheduled posts that are due for publication.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for scheduled posts...');
        
        $posts = Post::where('status', 'scheduled')
        ->where('scheduled_time', '<=', Carbon::now())
        ->get(); 
        \Illuminate\Support\Facades\Log::error( Carbon::now());

        if ($posts->isEmpty()) {
            $this->info('No scheduled posts found to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$posts->count()} posts to schedule for publication.");

        foreach ($posts as $post) {
            // Dispatch the job for each post.
            // The job will be pushed to the default queue.
            PublishPostJob::dispatch($post);

         
            $this->info("Dispatched job for Post ID: {$post->id}");
        }

        $this->info('All due scheduled posts have been dispatched to the queue.');
        return Command::SUCCESS;
    }
}

