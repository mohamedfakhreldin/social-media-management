<?php

namespace Database\Factories;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformFactory extends Factory
{
    protected $model = Platform::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'char_limit'=>300,
            'type' => $this->faker->randomElement(['twitter', 'instagram', 'linkedin', 'facebook']),
        ];
    }
} 