<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Relatorios;

use App\Models\Ticket;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class TicketsByCompanyReportQuery extends Query
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
        'name' => '/Relatorios/TicketsByCompanyReportQuery',
        'description' => 'Relatorio para visualizar a quantidade de Ticket por Empresa'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('TicketsByCompanyReport'));
    }

    public function args(): array
    {
        return [

        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $companies = Ticket::distinct()->pluck('client');

        $report = [];

        foreach ($companies as $company) {
            $tickets = Ticket::where('client', $company)->get();

            $totalTickets = $tickets->count();
            $openTickets = $tickets->where('status', false)->count();
            $resolvedTickets = $totalTickets - $openTickets;
            $resolvedPercentage = ($totalTickets > 0) ? ($resolvedTickets / $totalTickets) * 100 : 0;
            $averageResolutionTime = $tickets->filter(function ($ticket) {
                return $ticket->status === true;
            })->avg(function ($ticket) {
                return $ticket->updated_at->diffInMinutes($ticket->created_at);
            });

            $report[] = [
                'companyName' => $company,
                'totalTickets' => $totalTickets,
                'openTickets' => $openTickets,
                'resolvedTickets' => $resolvedTickets,
                'resolvedPercentage' => $resolvedPercentage,
                'averageResolutionTime' => $averageResolutionTime ?? 0,
            ];
        }

        return $report;
    }
}
