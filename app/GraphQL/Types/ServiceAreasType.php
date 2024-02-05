<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\ServiceAreas;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ServiceAreasType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ServiceArea',
        'description' => 'Endpoint para áreas de serviço',
        'model' => ServiceAreas::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID da área de serviço',
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID do usuário associado à área de serviço',
            ],
            'service_area' => [
                'type' => Type::string(),
                'description' => 'Nome da área de serviço',
            ],
            'user' => [
                'type' => GraphQL::type('User'),
                'description' => 'Usuário associado à área de serviço',
                'resolve' => function ($root, $args) {
                    return $root->user;
                },
            ],
        ];
    }
}
