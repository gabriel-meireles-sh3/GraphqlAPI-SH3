<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Relatorios;

use App\Models\Service;
use App\Models\Ticket;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class TicketsByAreaReportQuery extends Query
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        try {
            $this->auth = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return false;
        }
        return (bool) $this->auth;
    }
    protected $attributes = [
        'name' => '/Relatorios/TicketsByAreaReport',
        'description' => 'Relatorio para visualizar a quantidade de ticket por area'
    ];

    public function type(): Type
    {
        return Type::listOf(Type::nonNull(GraphQL::type('TicketsByAreaReport')));
    }

    public function args(): array
    {
        return [

        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $serviceAreas = Service::distinct()->pluck('service_area');

        $report = [];

        foreach ($serviceAreas as $area) {
            $tickets = Ticket::whereHas('services', function ($query) use ($area) {
                $query->where('service_area', $area);
            })->get();

            $totalTickets = $tickets->count();
            $openTickets = $tickets->where('status', false)->count();
            $resolvedTickets = $tickets->where('status', true)->count();
            $resolvedPercentage = $totalTickets > 0 ? ($resolvedTickets / $totalTickets) * 100 : 0;
            $averageResolutionTime = $this->calculateAverageResolutionTime($tickets);

            $report[] = [
                'serviceArea' => $area,
                'totalTickets' => $totalTickets,
                'openTickets' => $openTickets,
                'resolvedTickets' => $resolvedTickets,
                'resolvedPercentage' => $resolvedPercentage,
                'averageResolutionTime' => $averageResolutionTime,
            ];
        }

        return $report;
    }

    private function calculateAverageResolutionTime($tickets)
    {
        $resolvedTickets = $tickets->filter(function ($ticket) {
            return $ticket->status === true;
        });

        if ($resolvedTickets->isEmpty()) {
            return 0;
        }

        $totalResolutionTime = $resolvedTickets->sum(function ($ticket) {
            return Carbon::parse($ticket->updated_at)->diffInMinutes($ticket->created_at);
        });

        return $totalResolutionTime / $resolvedTickets->count();
    }
}
