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
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Rebing\GraphQL\Support\Facades\GraphQL;

class CollectivePerformanceReportQuery extends Query
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
        'name' => '/Relatorios/CollectivePerformanceReportQuery',
        'description' => 'Relatorio do rendimento coletivo para visualização de performace'
    ];

    public function type(): Type
    {
        return Type::listOf(Type::nonNull(GraphQL::type('CollectivePerformance')));
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
            $resolvedTicketsCount = $tickets->where('status', true)->count();
            $pendingTicketsCount = $totalTickets - $resolvedTicketsCount;

            $totalResolutionTime = 0;
            foreach ($tickets as $ticket) {
                if ($ticket->status) {
                    $totalResolutionTime += Carbon::parse($ticket->updated_at)->diffInSeconds($ticket->created_at);
                }
            }
            $averageResolutionTime = $resolvedTicketsCount > 0 ? $totalResolutionTime / $resolvedTicketsCount : 0;
            
            $resolvedPercentage = $totalTickets > 0 ? ($resolvedTicketsCount / $totalTickets) * 100 : 0;
            $ticketsOpenedCount = $tickets->where('status', false)->count();

            $report[] = [
                'serviceArea' => $area,
                'averageResolutionTime' => $averageResolutionTime,
                'resolvedTicketsCount' => $resolvedTicketsCount,
                'pendingTicketsCount' => $pendingTicketsCount,
                'resolvedPercentage' => $resolvedPercentage,
                'ticketsOpenedCount' => $ticketsOpenedCount,
            ];
        }

        return $report;
    }
}
