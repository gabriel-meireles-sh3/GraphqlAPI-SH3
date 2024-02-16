<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Support;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Service::class;

    public function definition()
    {

        $clientIds = Ticket::pluck('id')->toArray();
        $requester_names = Ticket::pluck('name')->toArray();
        $supportIds = Support::pluck('id')->toArray();
        $supportAreas = Support::whereNotNull('service_area')->pluck('service_area')->toArray();
        
        return [
            'requester_name' => $this->faker->randomElement($requester_names),
            'client_id' => $this->faker->randomElement($clientIds),
            'service_area' => $this->faker->randomElement($supportAreas),
            'support_id' => $this->faker->randomElement($supportIds),
            'status' => false,
        ];
    }
}
