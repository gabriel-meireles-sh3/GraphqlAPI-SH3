<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\User;

use App\GraphQL\Types\UserType;
use App\Models\User;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class RegisterMutation extends Mutation
{
    protected $attributes = [
        'name' => 'user/Register',
        'description' => 'Mutation para o registro do usuário na aplicação'
    ];

    public function type(): Type
    {
        return GraphQL::type('User');
    }

    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required'],
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'email', 'unique:users'],
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'min:6', 'max:50'],
            ],
            'role' => [
                'name' => 'role',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'integer', 'in:1,2,3,4'],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $user = User::create([
            'name' => $args['name'],
            'role' => $args['role'],
            'email' => $args['email'],
            'password' => bcrypt($args['password']),
        ]);

        if($user == null){
            throw new Exception('Erro ao registrar Usuário');
        }

        return $user;
    }
}
