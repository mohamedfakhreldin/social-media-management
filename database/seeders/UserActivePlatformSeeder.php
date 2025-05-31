<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Platform;
use App\Models\UserActivePlatform;
use Illuminate\Database\Seeder;

class UserActivePlatformSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $platforms = Platform::all();

        // For each user, connect to 2-4 random platforms
        foreach ($users as $user) {
            $randomPlatforms = $platforms->random(rand(2, 4));
            
            foreach ($randomPlatforms as $platform) {
                UserActivePlatform::create([
                    'user_id' => $user->id,
                    'platform_id' => $platform->id,
                    'is_active' => true,
              
                ]);
            }
        }
    }
} 