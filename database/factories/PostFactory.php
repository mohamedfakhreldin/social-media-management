<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        $status = $this->faker->randomElement(['draft', 'scheduled', 'published']);
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'image_url' => $this->faker->imageUrl,
            'scheduled_time' => $status=='scheduled'?  $this->faker->dateTimeBetween('now', '+1 week'):now(),
            'status' => $this->faker->randomElement(['draft', 'scheduled', 'published']),
            'user_id' => User::factory(),
        ];
    }
} 