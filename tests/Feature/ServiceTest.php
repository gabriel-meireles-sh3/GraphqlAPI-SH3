<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceAreas;
use App\Models\Support;
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
        $user = User::factory()->create(['role' => User::ROLE_ATTENDANT]);
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
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
                    "createService" => [
                        'requester_name' => $service->requester_name,
                        'client_id' => $service->client_id,
                        'service_area' => $service->service_area,
                        'support_id' => $service->support_id,
                    ],
                ],
            ]);
    }

    public function test_mutation_updateService()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory(5)->create();
        Ticket::factory(5)->create();
        $service = Service::factory()->create();

        $clientIds = Ticket::pluck('id')->toArray();
        $requester_names = Ticket::pluck('name')->toArray();
        $supportIds = Support::pluck('id')->toArray();
        $supportAreas = Support::whereNotNull('service_area')->pluck('service_area')->toArray();

        $newServiceData = [
            'id' => $service->id,
            'requester_name' =>  $this->faker->randomElement($requester_names),
            'client_id' => $this->faker->randomElement($clientIds),
            'service_area' => $this->faker->randomElement($supportAreas),
            'support_id' =>  $this->faker->randomElement($supportIds),
        ];

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('updateService', [
                'id' => $newServiceData['id'],
                'requester_name' => $newServiceData['requester_name'],
                'client_id' => $newServiceData['client_id'],
                'service_area' => $newServiceData['service_area'],
                'support_id' =>  $newServiceData['support_id'],
            ], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
            ->assertJson([
                'data' => [
                    "updateService" => [
                        'id' => $newServiceData['id'],
                        'requester_name' => $newServiceData['requester_name'],
                        'client_id' => $newServiceData['client_id'],
                        'service_area' => $newServiceData['service_area'],
                        'support_id' =>  $newServiceData['support_id'],
                    ],
                ],
            ]);
    }

    public function test_mutation_removeService()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
        Ticket::factory()->create();
        $service = Service::factory()->create();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('removeService', [
                'id' => $service->id,
            ], [])
            ->assertJson([
                'data' => [
                    "removeService" => true,
                ]
            ]);
    }

    public function test_mutation_restoreService()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
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
        Support::factory()->create();
        Ticket::factory()->create();
        $services = Service::factory(5)->create();

        $expectedServices = $services->map(function ($service) {
            return [
                'client_id' => $service->client_id,
                'id' => $service->id,
                'requester_name' => $service->requester_name,
                'service_area' => $service->service_area,
                'support_id' => $service->support_id,
            ];
        })->toArray();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('services', [], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
            ->assertJson([
                'data' => [
                    'services' => $expectedServices,
                ],
            ]);
    }

    public function test_query_service()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
        $ticket = Ticket::factory()->create();
        $service = Service::factory(5)->create([
            'requester_name' => $ticket->name,
        ]);
        $randomService = $service->random();
        $id = $randomService->id;

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('service', ['id' => $id], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
            ->assertJson([
                'data' => [
                    'service' => [
                        'client_id' => $randomService->client_id,
                        'id' => $randomService->id,
                        'service_area' => $randomService->service_area,
                        'support_id' => $randomService->support_id,
                    ],
                ]
            ]);
    }

    public function test_query_servicesBySupportId()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $supportUser = User::factory()->create(['role' => User::ROLE_SUPPORT]);
        $support = Support::factory()->create(['user_id' => $supportUser->id]);
        Ticket::factory(5)->create();
        $services = Service::factory(5)->create(["support_id" => $support->id]);
        $id = $support->id;

        $expectedServices = $services->map(function ($service) {
            return [
                'client_id' => $service->client_id,
                'id' => $service->id,
                'requester_name' => $service->requester_name,
                'service_area' => $service->service_area,
                'support_id' => $service->support_id,
            ];
        })->toArray();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('servicesBySupportId', ['support_id' => "$id"], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
            ->assertJson([
                'data' => [
                    "servicesBySupportId" => $expectedServices,
                ]
            ]);
    }

    public function test_query_servicesByTicketId()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
        $ticket = Ticket::factory()->create();
        $services = Service::factory(5)->create(['client_id' => $ticket->id]);
        $id = $ticket->id;

        $expectedServices = $services->map(function ($service) {
            return [
                'client_id' => $service->client_id,
                'id' => $service->id,
                'requester_name' => $service->requester_name,
                'service_area' => $service->service_area,
                'support_id' => $service->support_id,
            ];
        })->toArray();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('servicesByTicketId', ['ticket_id' => "$id"], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
            ->assertJson([
                'data' => [
                    "servicesByTicketId" => $expectedServices,
                ]
            ]);
    }

    public function test_mutation_serviceAssociate()
    {
        $supportUser = User::factory()->create(['role' => User::ROLE_SUPPORT]);
        $token = auth()->login($supportUser);

        Ticket::factory()->create();
        $support = Support::factory()->create(['user_id' => $supportUser->id, "service_area" => "service"]);
        $service = Service::factory()->create(['support_id' => NULL, "service_area" => $support->service_area]);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('associateService', [
                'service_id' => $service->id,
            ], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
            ->assertJson([
                'data' => [
                    "associateService" => [
                        'client_id' => "$service->client_id",
                        'id' => $service->id,
                        'requester_name' => $service->requester_name,
                        'service_area' => $service->service_area,
                        'support_id' => "$support->id",
                    ],
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
            ->assertJsonStructure([
                'data' => [
                    "servicesAreas" => [
                        '*' => [
                            'service_area',
                        ],
                    ],
                ],
            ]);
    }

    public function test_query_servicesTypes()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
        Ticket::factory()->create();

        $serviceTypes = ['TypeA', 'TypeB', 'TypeC'];
        foreach ($serviceTypes as $area) {
            Service::factory()->create(['service' => $area, 'status' => true]);
        }

        $response = $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('servicesTypes', [], ['service'])
            ->assertJsonStructure([
                'data' => [
                    "servicesTypes" => [
                        '*' => [
                            'service',
                        ],
                    ],
                ],
            ]);
    }

    public function test_query_services_Unassociated()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
        Ticket::factory()->create();

        $serviceWithoutSupport = Service::factory()->create(['support_id' => NULL]);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query(
                'servicesUnassociated',
                [],
                ['id', 'requester_name', 'client_id', 'service_area', 'support_id']
            )
            ->assertJson([
                'data' => [
                    "servicesUnassociated" => [
                        [
                            'id' => $serviceWithoutSupport->id,
                            'requester_name' => $serviceWithoutSupport->requester_name,
                            'client_id' => "$serviceWithoutSupport->client_id",
                            'service_area' => $serviceWithoutSupport->service_area,
                            'support_id' => $serviceWithoutSupport->support_id,
                        ],
                    ],
                ],
            ]);
    }

    public function test_mutation_serviceComplete()
    {
        $supportUser = User::factory()->create(['role' => User::ROLE_SUPPORT]);
        $token = auth()->login($supportUser);

        Ticket::factory()->create();
        $support = Support::factory()->create();
        $service = Service::factory()->create(['support_id' => $support->id]);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('completeService', [
                'service_id' => $service->id,
                'service' => $this->faker->word(),
            ], ['id', 'requester_name', 'client_id', 'service_area', 'support_id'])
            ->assertJson([
                'data' => [
                    "completeService" => [
                        'id' => $service->id,
                        'requester_name' => $service->requester_name,
                        'client_id' => "$service->client_id",
                        'service_area' => $service->service_area,
                        'support_id' => $service->support_id,
                    ],
                ],
            ]);
    }

    public function test_query_serviceIncomplete()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
        Ticket::factory()->create();

        $service = Service::factory()->create(['status' => false]);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query(
                'servicesIncomplete',
                [],
                ['id', 'requester_name', 'client_id', 'service_area', 'support_id']
            )
            ->assertJson([
                'data' => [
                    "servicesIncomplete" => [
                        [
                        'id' => $service->id,
                        'requester_name' => $service->requester_name,
                        'client_id' => "$service->client_id",
                        'service_area' => $service->service_area,
                        'support_id' => $service->support_id,
                        ],
                    ],
                ],
            ]);
    }
}
