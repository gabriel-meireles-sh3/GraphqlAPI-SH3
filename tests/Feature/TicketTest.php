<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TicketTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;
    use WithFaker;

    public function test_mutation_createTicket()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $ticketData = [
            'name' => $this->faker->name,
            'client' => $this->faker->company,
            'occupation_area' => $this->faker->jobTitle,
        ];

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('createTicket', [
                'name' => $ticketData['name'],
                'client' => $ticketData['client'],
                'occupation_area' => $ticketData['occupation_area'],
            ], ['id', 'name', 'client', 'occupation_area'])
            ->assertJson([
                'data' => [
                    "createTicket" => true,
                ],
            ]);
    }

    public function test_mutation_updateTicket()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $ticket = Ticket::factory()->create();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('updateTicket', [
                'name' => 'Nome da pessoa',
                'client' => 'Nome da empresa',
                'occupation_area' => 'Area para suporte',
                'id' => $ticket->id
            ], ['id', 'name', 'client', 'occupation_area'])
            ->assertJson([
                'data' => [
                    "updateTicket" => true,
                ],
            ]);
    }

    public function test_mutation_removeTicket()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->mutation('removeTicket', [
            'id' => Ticket::factory()->create()->id,
        ], [])
        ->assertJson([
            'data' => [
                "removeTicket" => true,
            ]
        ]);
    }

    public function test_mutation_restoreTicket()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $ticket = Ticket::factory()->create();
        $id = $ticket->id;
        $ticket->delete();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
        ->mutation('restoreTicket', [
            'id' => $id,
        ], [])
        ->assertJson([
            'data' => [
                "restoreTicket" => true,
            ]
        ]);
    }
}
