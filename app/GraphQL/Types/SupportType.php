<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Support;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Symfony\Component\VarDumper\VarDumper;

class SupportType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ServiceArea',
        'description' => 'Endpoint para áreas de serviço',
        'model' => Support::class,
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
                'description' => 'Área de serviço associadas ao analista de suporte',
            ],
            'user' => [
                'type' => GraphQL::type('User'),
                'description' => 'Usuário associado à área de serviço',
                'resolve' => function ($root, $args) {
                    return $root->user;
                },
            ],
            'associated_services' => [
                'type' => Type::listOf(GraphQL::type('Service')),
                'description' => 'Lista de serviços que o analista de suporte está atendendo nessa área',
                'selectable' => false,
                'resolve' => function ($root, $args) {
                    return $root->associated_services;
                }
            ],
        ];
    }
}
