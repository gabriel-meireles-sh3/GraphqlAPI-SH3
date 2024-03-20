<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;

class CollectivePerformanceType extends GraphQLType
{
    protected $attributes = [
        'name' => 'CollectivePerformance',
        'description' => 'Tipo para representar o relatório de rendimento coletivo',
    ];

    public function fields(): array
    {
        return [
            'serviceArea' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'A área de serviço associada ao relatório',
            ],
            'averageResolutionTime' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'A média de tempo de resolução dos tickets (em horas)',
            ],
            'resolvedTicketsCount' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'O número total de tickets resolvidos',
            ],
            'pendingTicketsCount' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'O número total de tickets em espera de resolução',
            ],
            'resolvedPercentage' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'A porcentagem de tickets resolvidos em relação ao total de tickets',
            ],
            'ticketsOpenedCount' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'O número total de tickets abertos',
            ],
        ];
    }
}
