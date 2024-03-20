<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Ticket;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TicketType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Ticket',
        'description' => 'A type',
        'model' => Ticket::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'description' => 'O ID do usuário dentro do banco de dados'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'O nome do solicitante',
            ],
            'client' => [
                'type' => Type::string(),
                'description' => 'O cliente a qual o solicitante pertence',
            ],
            'occupation_area' => [
                'type' => Type::string(),
                'description' => 'A area onde o solicitante querer atendimento',
            ],
            'services' => [
                'type' => Type::listOf(GraphQL::type('Service')),
                'description' => 'Serviço associados ao client(empresa)',
                'selectable' => false,
                'resolve' => function ($root, $args) {
                    return $root->services;
                }
            ]
        ];
    }
}
