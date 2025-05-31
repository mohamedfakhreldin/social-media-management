<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use App\Models\Platform;

class PostSeeder extends Seeder
{
    public function run()
    {
        // Get all users
        $users = User::all();

        // Create posts for each user
        foreach ($users as $user) {
            // Get user's platforms
            $userPlatforms = $user->platforms;
            
            // Skip if user has no platforms
            if ($userPlatforms->isEmpty()) {
                continue;
            }

            // Create 3 posts per user
            $posts = Post::factory()
                ->count(3)
                ->create([
                    'user_id' => $user->id
                ]);

            // Attach user's platforms to each post
            foreach ($posts as $post) {
                // Randomly attach 1-3 of user's platforms to each post
                $randomPlatforms = $userPlatforms->random(min(rand(1, 3), $userPlatforms->count()));
                $post->platforms()->attach($randomPlatforms);
            }
        }
    }
} 