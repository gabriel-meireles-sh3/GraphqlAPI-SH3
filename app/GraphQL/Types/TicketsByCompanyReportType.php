<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TicketsByCompanyReportType extends GraphQLType
{
    protected $attributes = [
        'name' => 'TicketsByCompanyReport',
        'description' => 'Type for Tickets by Company Report',
    ];

    public function fields(): array
    {
        return [
            'companyName' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The name of the company associated with the tickets',
            ],
            'totalTickets' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The total number of tickets opened by the company',
            ],
            'openTickets' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The number of tickets still open for the company',
            ],
            'resolvedTickets' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The number of tickets resolved by the company',
            ],
            'resolvedPercentage' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'The percentage of resolved tickets compared to total tickets opened by the company',
            ],
            'averageResolutionTime' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'The average resolution time for tickets resolved by the company (in minutes)',
            ],
        ];
    }
}