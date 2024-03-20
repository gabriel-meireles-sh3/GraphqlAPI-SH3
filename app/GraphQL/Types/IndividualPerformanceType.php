<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class IndividualPerformanceType extends GraphQLType
{
    protected $attributes = [
        'name' => 'IndividualPerformance',
        'description' => 'Type for individual performance report'
    ];

    public function fields(): array
    {
        return [
            'analystName' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The name of the support analyst',
            ],
            'totalTickets' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The total number of tickets assigned to the analyst',
            ],
            'ticketsResolved' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The number of tickets resolved by the analyst',
            ],
            'currentTicket' => [
                'type' => Type::string(),
                'description' => 'The ticket currently assigned to the analyst',
            ],
            'resolutionPercentage' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'The percentage of tickets resolved by the analyst',
            ],
            'averageResolutionTime' => [
                'type' => Type::string(),
                'description' => 'The average resolution time of tickets for the analyst',
            ],
        ];
    }
}
