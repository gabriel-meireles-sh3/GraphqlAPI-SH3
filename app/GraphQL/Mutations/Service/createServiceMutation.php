<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Service;

use App\GraphQL\Validations\ServiceValidation;
use App\Models\Service;
use App\Models\User;
use App\Utils\AuthUtils;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class createServiceMutation extends Mutation
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $allowedRoles = [User::ROLE_ATTENDANT, User::ROLE_ADMIN];
        try{
            $this->auth = AuthUtils::checkAuthenticationAndRoles($allowedRoles);
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
                'type' => Type::nonNull(Type::string()),
                'description' => 'O nome do requisitante no banco Tickets'
            ],
            'client_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'O ID do client (empresa) no banco Tickets'
            ],
            'service_area' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O área de suporte para o Ticket'
            ],
            'support_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'O ID do analista de suporte no banco Supports'
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $validator = ServiceValidation::make($args);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            throw ValidationException::withMessages($errors);
        }

        $service = new Service();
        $service->fill($args);
        $service->save();

        return $service;
    }
}
