<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Platform;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Event::fake(); // Completely disables all event firing
        // Create a test user
        $this->user = User::factory()->create();
        
        // Create some test platforms
        $this->platforms = Platform::factory()->count(3)->create();
    }

    public function test_can_create_post()
    {
        $postData = [
            'title' => 'Test Post',
            'content' => 'This is a test post content',
            'image_url' => 'https://example.com/image.jpg',
            'scheduled_time' => now()->addDay(),
            'status' => 'scheduled',
            'platforms' => $this->platforms->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/posts', $postData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'content',
                'image_url',
                'scheduled_time',
                'status',
                'platforms',
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => $postData['title'],
            'content' => $postData['content'],
            'status' => $postData['status'],
        ]);
    }

    public function test_can_list_posts()
    {
        $posts = Post::factory()
            ->count(3)
            ->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonCount(13);
    }

    public function test_can_filter_posts_by_status()
    {
        Post::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'draft'
        ]);

        Post::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published'
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/posts?status=draft');

        $response->assertStatus(200)
            ->assertJsonCount(13);
    }

    public function test_can_update_post()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'status'=>'scheduled'
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status' => 'published',
            'platforms'=>$this->platforms->pluck('id')->toArray()
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'title' => $updateData['title'],
                'content' => $updateData['content'],
                'status' => $updateData['status'],
            ]);
    }

    public function test_can_delete_post()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
} 