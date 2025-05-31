<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            [
                'name' => 'Facebook',
                'type' => 'facebook',
                'char_limit' => 63206,
            ],
            [
                'name' => 'Twitter',
                'type' => 'twitter',
                'char_limit' => 280,
            ],
            [
                'name' => 'LinkedIn',
                'type' => 'linkedin',
                'char_limit' => 3000,
            ],
            [
                'name' => 'Instagram',
                'type' => 'instagram',
                'char_limit' => 2200,
            ],
            [
                'name' => 'Pinterest',
                'type' => 'pinterest',
                'char_limit' => 500,
            ],
                
        ];

        foreach ($platforms as $platform) {
        }
        Platform::insert($platforms);
    }
} 