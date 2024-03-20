<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Relatorios;

use App\Models\Support;
use App\Models\Ticket;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndividualPerformanceReportQuery extends Query
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
        'name' => '/Relatorios/IndividualPerformanceReportQuery',
        'description' => 'Relatorio do rendimento individual para visualização de performace'
    ];

    public function type(): Type
    {
        return Type::listOf(Type::nonNull(GraphQL::type('IndividualPerformance')));
    }

    public function args(): array
    {
        return [

        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $analysts = Support::all();
        $report = [];

        foreach ($analysts as $analyst) {
            $tickets = Ticket::whereHas('services', function ($query) use ($analyst) {
                $query->where('support_id', $analyst->id);
            })->get();
            $totalTickets = $tickets->count();
            $resolvedTickets = $tickets->where('status', true)->count();
            $currentTicket = $tickets->where('status', false)->first();

            $resolutionPercentage = $totalTickets > 0 ? ($resolvedTickets / $totalTickets) * 100 : 0;

            $resolvedTicketsFilter = $tickets->filter(function ($ticket) {
                return $ticket->status === true;
            });
            $averageResolutionTime = $resolvedTicketsFilter->isNotEmpty() ? $resolvedTicketsFilter->avg(function ($ticket) {
                return Carbon::parse($ticket->updated_at)->diffInMinutes($ticket->created_at);
            }) : 0;

            $report[] = [
                'analystName' => $analyst->user->name,
                'totalTickets' => $totalTickets,
                'ticketsResolved' => $resolvedTickets,
                'currentTicket' => $currentTicket ? $currentTicket->name : null,
                'resolutionPercentage' => round($resolutionPercentage, 2),
                'averageResolutionTime' => round($averageResolutionTime, 2),
            ];
        }

        return $report;
    }
}
