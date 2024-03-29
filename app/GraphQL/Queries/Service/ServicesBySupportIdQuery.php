<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Service;

use App\Models\Service;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ServicesBySupportIdQuery extends Query
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
        'name' => 'service/ServicesBySupportId',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Service'));
    }

    public function args(): array
    {
        return [
            'support_id' => [
                'name' => 'support_id',
                'type' => Type::string(),
                'rules' => ['required', 'exists:supports,id,deleted_at,NULL'],
                'description' => 'O ID dentro do banco supports'
            ]
        ];
    }

    public function validationErrorMessages(array $args = []): array
    {
        return [
            'service_id.exists' => 'Support ID não encontrado',
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $fields)
    {
        // Obtenha os campos e relações selecionados
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $services = Service::where('support_id', $args['support_id'])->select($select)->with($with)->get();

        if ($services->isEmpty()){
            return null;
        }
        
        return $services;
    }
}
