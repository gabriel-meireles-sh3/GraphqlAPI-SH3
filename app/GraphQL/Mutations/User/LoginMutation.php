<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\User;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginMutation extends Mutation
{
    protected $attributes = [
        'name' => 'user/Login',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return Type::string();
    }

    public function args(): array
    {
        return [
            'email' => [
              'name' => 'email',
              'type' => Type::nonNull(Type::string()),
              'rules' => ['required', 'email'],
            ],
            'password' => [
              'name' => 'password',
              'type' => Type::nonNull(Type::string()),
              'rules' => ['required'],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $credentials = [
            'email' => $args['email'],
            'password' => $args['password']
        ];

        $token = JWTAuth::attempt($credentials);

        if ($token == null) {
            throw new \Exception('Usuário ou senha inválidos.');
        }

        return $token;
    }
}
