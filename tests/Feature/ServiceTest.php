<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    use WithFaker;

    public function test_mutation_createService()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        
        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Ticket::factory()->create();
        $service = Service::factory()->create();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('createService', [
                'requester_name' => $service->requester_name,
                'client_id' => $service->client_id,
                'service_area' => $service->service_area,
                'support_id' => $service->support_id,
            ], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
            ->assertJson([
                'data' => [
                    "createService" => true,
                ],
            ]);
    }

    public function test_mutation_updateService(){
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Ticket::factory()->create();
        $service = Service::factory()->create();

        $clientIds = Ticket::pluck('id')->toArray();
        $supportIds = User::where('role', User::ROLE_SUPPORT)->pluck('id')->toArray();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('updateService', [
                'id' => $service->id,
                'requester_name' => $this->faker->name(),
                'client_id' => $this->faker->randomElement($clientIds),
                'service_area' => $this->faker->word(),
                'support_id' =>  $this->faker->randomElement($supportIds),
            ], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
            ->assertJson([
                'data' => [
                    "updateService" => true,
                ],
            ]);
    }
    
    public function test_mutation_removeService()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Ticket::factory()->create();
        
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->mutation('removeService', [
            'id' => Service::factory()->create()->id,
        ], [])
        ->assertJson([
            'data' => [
                "removeService" => true,
            ]
        ]);
    }

    public function test_mutation_restoreService()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Ticket::factory()->create();
        $service = Service::factory()->create();
        $id = $service->id;
        $service->delete();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->mutation('restoreService', [
            'id' => $id,
        ], [])
        ->assertJson([
            'data' => [
                "restoreService" => true,
            ]
        ]);
    }

    public function test_query_services()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Ticket::factory()->create();
        $service = Service::factory(5)->create();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->query('services', [], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
        ->assertJson([
            'data' => [
                "services" => true,
            ]
        ]);
    }

    public function teste_query_service()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Ticket::factory()->create();
        $service = Service::factory(5)->create();
        $randomService = $service->random();
        $id = $randomService->id;

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->query('service', ['id' => $id], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
        ->assertJson([
            'data' => [
                "service" => true,
            ]
        ]);
    }
}
