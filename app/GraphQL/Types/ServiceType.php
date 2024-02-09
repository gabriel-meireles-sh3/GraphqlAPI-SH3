<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Service;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ServiceType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Service',
        'description' => 'A type',
        'model' => Service::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'description' => 'O ID do serviço dentro do banco de dados'
            ],
            'requester_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O name do usuário que requisitou o suporte'
            ],
            'client_id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O ID do client relacionado a ordem de serviço dentro do banco de dados'
            ],
            'service_area' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'A área de atendimento que o serviço requer'
            ],
            'support_id' => [
                'type' => Type::string(),
                'description' => 'O ID do usuário analista de suporte que atende a ordem'
            ],
            'status' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'O status do serviço (false => em andamento | true => finalizado)',
            ],
            'service' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O serviço que o usuário analista de suporte executou'
            ],
            'ticket' => [
                'type' => GraphQL::type('Ticket'),
                'description' => 'Ticket associado ao serviço (suporte)',
                'selectable' => false,
            ],
            'user' => [
                'type' => GraphQL::type('Support'),
                'description' => 'Support associado à ordem de serviço',
                'resolve' => function ($root, $args) {
                    return $root->user;
                },
            ],
        ];
    }
}
