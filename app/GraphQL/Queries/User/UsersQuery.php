<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\User;

use App\Models\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersQuery extends Query
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        try{
            $this->auth = JWTAuth::parseToken()->authenticate();
        } catch(JWTException $e){
            return false;
        }
        return (bool) $this->auth;
    }
    
    protected $attributes = [
        'name' => 'user/Users',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('User'));
    }

    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'description' => 'Pesquisa por nome de usuario',
                'type' => Type::string(),
                'defaultValue' => '',
            ],

        ];
    }


    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $fields)
    {
        // Obtenha os campos e relações selecionados
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $query = User::query();

        $query->select($select)->with($with)->where('name', 'like', "%{$args['name']}%");

        return $query->get();
    }
}
