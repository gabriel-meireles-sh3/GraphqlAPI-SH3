<?php

namespace Database\Factories;

use App\Models\ServiceAreas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ServiceAreasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ServiceAreas::class;

    public function definition(): array
    {
        $supportIds = User::where('role', User::ROLE_SUPPORT)->pluck('id')->toArray();
        
        return [
            'user_id' => $this->faker->randomElement($supportIds),
            'service_area' => $this->faker->word,
        ];
    }
}
