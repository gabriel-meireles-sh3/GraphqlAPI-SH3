<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Support;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function testCollectivePerformanceReport()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
        Ticket::factory()->create();
        $services = Service::factory(5)->create();

        $response = $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('rendimentoColetivo', [], [
                'serviceArea',
                'averageResolutionTime',
                'resolvedTicketsCount',
                'pendingTicketsCount',
                'resolvedPercentage',
                'ticketsOpenedCount'
            ]);

        $response->assertJsonStructure([
            'data' => [
                'rendimentoColetivo' => [
                    [
                        'serviceArea',
                        'averageResolutionTime',
                        'resolvedTicketsCount',
                        'pendingTicketsCount',
                        'resolvedPercentage',
                        'ticketsOpenedCount'
                    ]
                ]
            ]
        ]);
    }

    public function testIndividualPerformanceReport()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
        Ticket::factory()->create();
        $services = Service::factory(5)->create();

        $response = $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('rendimentoIndividual', [], [
                'analystName',
                'totalTickets',
                'ticketsResolved',
                'currentTicket',
                'resolutionPercentage',
                'averageResolutionTime'
            ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'rendimentoIndividual' => [
                    [
                        'analystName',
                        'totalTickets',
                        'ticketsResolved',
                        'currentTicket',
                        'resolutionPercentage',
                        'averageResolutionTime'
                    ]
                ]
            ]
        ]);
    }

    public function testTicketsByAreaReport()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
        Ticket::factory()->create();
        $services = Service::factory(5)->create();

        $response = $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('TicketPorArea', [], [
                'serviceArea',
                'totalTickets',
                'openTickets',
                'resolvedTickets',
                'resolvedPercentage',
                'averageResolutionTime'
            ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'TicketPorArea' => [
                    [
                        'serviceArea',
                        'totalTickets',
                        'openTickets',
                        'resolvedTickets',
                        'resolvedPercentage',
                        'averageResolutionTime'
                    ]
                ]
            ]
        ]);
    }

    public function testTicketsByCompanyReport()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
        Ticket::factory()->create();
        $services = Service::factory(5)->create();

        $response = $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('TicketPorEmpresa', [], [
                'companyName',
                'totalTickets',
                'openTickets',
                'resolvedTickets',
                'resolvedPercentage',
                'averageResolutionTime'
            ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'TicketPorEmpresa' => [
                    [
                        'companyName',
                        'totalTickets',
                        'openTickets',
                        'resolvedTickets',
                        'resolvedPercentage',
                        'averageResolutionTime'
                    ]
                ]
            ]
        ]);
    }
}
