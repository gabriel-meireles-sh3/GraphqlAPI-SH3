<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Service;

use App\Models\Service;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class updateServiceMutation extends Mutation
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
        'name' => 'service/updateService',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Service');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:services,id,deleted_at,NULL'],
            ],
            'requester_name' => [
                'name' => 'requester_name',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required'],
            ],
            'client_id' => [
                'name' => 'client_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required'],
            ],
            'service_area' => [
                'name' => 'service_area',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required'],
            ],
            'support_id' => [
                'name' => 'support_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required'],
            ],
        ];
    }

    public function validationErrorMessages(array $args = []): array
    {
        return [
            'id.exists' => 'Ticket não encontrado',
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $service = Service::findOrFail($args['id']);
        $service->update($args);

        return $service;
    }
}