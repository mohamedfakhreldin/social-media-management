<?php

namespace Tests\Feature\Console;

use Tests\TestCase;
use App\Models\Post;
use Carbon\Carbon;
use App\Jobs\PublishPostJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;

class ProcessScheduledPostsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the queue to prevent actual job execution
        \Illuminate\Support\Facades\Event::fake(); // Completely disables all event firing
        Queue::fake();

        // Mock logging to capture log messages
        Log::shouldReceive('error')->andReturn(true);
    }

    /** @test */
    public function it_processes_scheduled_posts_due_for_publication()
    {
        // Arrange: Create scheduled posts
        $pastPost = Post::factory()->create([
            'status' => 'scheduled',
            'scheduled_time' => Carbon::now()->subHour(),
            'title' => 'Past Scheduled Post'
        ]);

        $currentPost = Post::factory()->create([
            'status' => 'scheduled',
            'scheduled_time' => Carbon::now(),
            'title' => 'Current Scheduled Post'
        ]);

        $futurePost = Post::factory()->create([
            'status' => 'scheduled',
            'scheduled_time' => Carbon::now()->addHour(),
            'title' => 'Future Scheduled Post'
        ]);

        // Act: Run the command
        $this->artisan('posts:process-scheduled')
        ->expectsOutput('Checking for scheduled posts...')
        ->expectsOutput('Found 2 posts to schedule for publication.')
        ->expectsOutput("Dispatched job for Post ID: {$pastPost->id}")
        ->expectsOutput("Dispatched job for Post ID: {$currentPost->id}")
        ->expectsOutput('All due scheduled posts have been dispatched to the queue.')
        ->assertExitCode(0);

        // Assert: Verify jobs were dispatched
        Queue::assertPushed(PublishPostJob::class, 2);
        
        Queue::assertPushed(PublishPostJob::class, 2);
        
        Queue::assertPushed(PublishPostJob::class, function ($job) use ($pastPost) {
            // Use reflection to access protected property
            $reflection = new \ReflectionClass($job);
            $property = $reflection->getProperty('post');
            $property->setAccessible(true);
            $post = $property->getValue($job);
            return $post->id === $pastPost->id;
        });

        Queue::assertPushed(PublishPostJob::class, function ($job) use ($currentPost) {
            // Use reflection to access protected property
            $reflection = new \ReflectionClass($job);
            $property = $reflection->getProperty('post');
            $property->setAccessible(true);
            $post = $property->getValue($job);
            return $post->id === $currentPost->id;
        });

        // Future post should not be processed
        Queue::assertNotPushed(PublishPostJob::class, function ($job) use ($futurePost) {
            // Use reflection to access protected property
            $reflection = new \ReflectionClass($job);
            $property = $reflection->getProperty('post');
            $property->setAccessible(true);
            $post = $property->getValue($job);
            return $post->id === $futurePost->id;
        });
    }

    /** @test */
    public function it_handles_no_scheduled_posts_gracefully()
    {
        // Arrange: Create posts that shouldn't be processed
        Post::factory()->create([
            'status' => 'published',
            'scheduled_time' => Carbon::now()->subHour()
        ]);

        Post::factory()->create([
            'status' => 'draft',
            'scheduled_time' => Carbon::now()->subHour()
        ]);

        // Act & Assert
        $this->artisan('posts:process-scheduled')
            ->expectsOutput('Checking for scheduled posts...')
            ->expectsOutput('No scheduled posts found to process.')
            ->assertExitCode(0);

        Queue::assertNothingPushed();
    }

    /** @test */
    public function it_only_processes_posts_with_scheduled_status()
    {
        // Arrange: Create posts with different statuses
        $scheduledPost = Post::factory()->create([
            'status' => 'scheduled',
            'scheduled_time' => Carbon::now()->subMinute()
        ]);

        $publishedPost = Post::factory()->create([
            'status' => 'published',
            'scheduled_time' => Carbon::now()->subMinute()
        ]);

        $draftPost = Post::factory()->create([
            'status' => 'draft',
            'scheduled_time' => Carbon::now()->subMinute()
        ]);

        // Act
        $this->artisan('posts:process-scheduled');

        // Assert: Only the scheduled post should be processed
        Queue::assertPushed(PublishPostJob::class, 1);
        Queue::assertPushed(PublishPostJob::class, function ($job) use ($scheduledPost) {
            // Use reflection to access protected property
            $reflection = new \ReflectionClass($job);
            $property = $reflection->getProperty('post');
            $property->setAccessible(true);
            $post = $property->getValue($job);
            return $post->id === $scheduledPost->id;
        });
    }
}