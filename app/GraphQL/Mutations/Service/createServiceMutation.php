<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Service;

use App\Models\Service;
use App\Models\User;
use App\Utils\AuthUtils;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class createServiceMutation extends Mutation
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $allowedRoles = [User::ROLE_ATTENDANT, User::ROLE_ADMIN];
        try{
            AuthUtils::checkAuthenticationAndRoles($allowedRoles);
        } catch(Exception $e){
            return false;
        }
        return (bool) $this->auth;
    }

    protected $attributes = [
        'name' => 'service/createService',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Service');
    }

    public function args(): array
    {
        return [
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

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $service = new Service();
        $service->fill($args);
        $service->save();

        return $service;
    }
}
