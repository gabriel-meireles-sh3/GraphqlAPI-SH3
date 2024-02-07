<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\GraphQL as GraphQLGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'User',
        'description' => 'Endpoint de usuários',
        'model' => User::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'O ID do usuário dentro do banco de dados'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'O nome do usuário dentro do banco de dados',
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'O e-mail do usuário dentro do banco de dados',
            ],
            'role' => [
                'type' => Type::string(),
                'description' => 'A role do usuário dentro do banco de dados',
            ],
            'service_areas' => [
                'type' => Type::listOf(GraphQL::type('ServiceAreas')),
                'description' => 'Áreas de serviço do usuário',
                'selectable' => false,
            ],
            'associated_services' => [
                'type' => Type::listOf(GraphQL::type('Service')),
                'description' => 'Lista de serviços que o analista de suporte está atendendo',
            ],
        ];
    }
}
