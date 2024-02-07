<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceAreas;
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
        Service::factory(5)->create();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->query('services', [], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
        ->assertJson([
            'data' => [
                "services" => true,
            ]
        ]);
    }

    public function test_query_service()
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

    public function test_query_servicesBySupportId()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $users = User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Ticket::factory(5)->create();
        Service::factory(5)->create();
        $id = $users->id;

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->query('servicesBySupportId', ['support_id' => $id], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
        ->assertJson([
            'data' => [
                "servicesBySupportId" => true,
            ]
        ]);
    }

    public function test_query_servicesByTicketId()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        $ticket = Ticket::factory()->create();
        Service::factory(5)->create();
        $id = $ticket->id;

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->query('servicesByTicketId', ['ticket_id' => $id], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
        ->assertJson([
            'data' => [
                "servicesByTicketId" => true,
            ]
        ]);
    }

    public function test_mutation_serviceAssociate()
    {   
        $supportUser = User::factory()->create(['role' => User::ROLE_SUPPORT]);
        $token = auth()->login($supportUser);
        Ticket::factory()->create();
        $service = Service::factory()->create(['support_id' => NULL, "service_area" => "service"]);
        ServiceAreas::factory()->create(['user_id' => $supportUser->id, 'service_area' => $service->service_area]);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('associateService', [
                'service_id' => $service->id,
            ], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
            ->assertJson([
                'data' => [
                    "associateService" => true,
                ],
            ]);

    }

    public function test_query_servicesAreas()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        Ticket::factory()->create();
        $serviceAreas = ['Support', 'Maintenance', 'Consulting'];

        foreach ($serviceAreas as $area) {
            Service::factory()->create(['service_area' => $area]);
        }

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->query('servicesAreas', [], ['service_area'])
        ->assertJson([
            'data' => [
                "servicesAreas" => true,
            ]
        ]);
    }

    public function test_query_servicesTypes()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        Ticket::factory()->create();
        $serviceTypes= ['TypeA', 'TypeB', 'TypeC'];
        foreach ($serviceTypes as $area) {
            Service::factory()->create(['service' => $area]);
        }

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->query('servicesTypes', [], ['service_area'])
        ->assertJson([
            'data' => [
                "servicesTypes" => true,
            ]
        ]);
    }

    public function test_query_services_Unassociated()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        Ticket::factory()->create();

        $serviceWithoutSupport = Service::factory()->create(['support_id' => NULL]);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->query('servicesUnassociated', 
        [], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
        ->assertJson([
            'data' => [
                "servicesUnassociated" => true,
            ]
        ]);
    }

    public function test_mutation_serviceComplete()
    {
        $supportUser = User::factory()->create(['role' => User::ROLE_SUPPORT]);
        $token = auth()->login($supportUser);
        Ticket::factory()->create();
        $service = Service::factory()->create(['support_id' => $supportUser->id]);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('completeService', [
                'service_id' => $service->id,
                'service' => $this->faker->word(),
            ], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
            ->assertJson([
                'data' => [
                    "completeService" => true,
                ],
            ]);
    }

    public function test_query_serviceIncomplete()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        Ticket::factory()->create();

        Service::factory()->create(['status' => false]);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->query('servicesIncomplete', 
        [], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
        ->assertJson([
            'data' => [
                "servicesIncomplete" => true,
            ]
        ]);
    }
}
