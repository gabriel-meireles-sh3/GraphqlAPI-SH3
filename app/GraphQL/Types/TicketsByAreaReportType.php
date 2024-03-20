<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TicketsByAreaReportType extends GraphQLType
{
    protected $attributes = [
        'name' => 'TicketsByArea',
        'description' => 'Type for Tickets by Area report',
    ];

    public function fields(): array
    {
        return [
            'serviceArea' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Service area name',
            ],
            'totalTickets' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Total number of tickets in this area',
            ],
            'openTickets' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Number of open tickets in this area',
            ],
            'resolvedTickets' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Number of resolved tickets in this area',
            ],
            'resolvedPercentage' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'Percentage of resolved tickets in this area',
            ],
            'averageResolutionTime' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'Average resolution time for tickets in this area (in minutes)',
            ],
        ];
    }
}
