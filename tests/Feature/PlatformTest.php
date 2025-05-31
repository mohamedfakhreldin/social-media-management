<?php

namespace Tests\Feature;

use App\Models\Platform;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_platforms()
    {
        $platforms = Platform::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/platforms');

        $response->assertStatus(200)
            ->assertJsonCount(13);
    }

    // public function test_can_create_platform()
    // {
    //     $platformData = [
    //         'name' => 'Test Platform',
    //         'type' => 'twitter',
    //         ''
    //     ];

    //     $response = $this->actingAs($this->user)
    //         ->postJson('/api/platforms', $platformData);

    //     $response->assertStatus(201)
    //         ->assertJsonStructure([
    //             'id',
    //             'name',
    //             'type',
    //         ]);

    //     $this->assertDatabaseHas('platforms', [
    //         'name' => $platformData['name'],
    //         'type' => $platformData['type'],
    //     ]);
    // }

    // public function test_can_update_platform()
    // {
    //     $platform = Platform::factory()->create();

    //     $updateData = [
    //         'name' => 'Updated Platform',
    //         'type' => 'instagram'
    //     ];

    //     $response = $this->actingAs($this->user)
    //         ->putJson("/api/platforms/{$platform->id}", $updateData);

    //     $response->assertStatus(200)
    //         ->assertJson([
    //             'name' => $updateData['name'],
    //             'type' => $updateData['type'],
    //         ]);
    // }

    // public function test_can_delete_platform()
    // {
    //     $platform = Platform::factory()->create();

    //     $response = $this->actingAs($this->user)
    //         ->deleteJson("/api/platforms/{$platform->id}");

    //     $response->assertStatus(204);
    //     $this->assertDatabaseMissing('platforms', ['id' => $platform->id]);
    // }
} 