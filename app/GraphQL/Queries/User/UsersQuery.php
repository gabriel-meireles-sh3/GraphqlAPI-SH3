<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\User;

use App\Models\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class UsersQuery extends Query
{
    protected $attributes = [
        'name' => 'user/Users',
    ];

    public function type(): Type
    {
        return Type::listOf(Type::string());
    }

    public function args(): array
    {
        return [
            'limite' => [
                'name' => 'limite',
                'description' => 'Numero limite de resultados, valor padrão: 50.',
                'type' => Type::int(),
                'defaultValue' => 20,
            ],

            'nome' => [
                'name' => 'nome',
                'description' => 'Pesquisa por nome de usuario',
                'type' => Type::string(),
                'defaultValue' => '',
            ],

        ];
    }


    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $query = User::query();

        $query->limit($args['limite']);
        $query->where('nome', 'like', "%{$args['nome']}%");

        return $query->get();
    }
}
