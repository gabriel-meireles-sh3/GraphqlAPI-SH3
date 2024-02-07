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

class removeServiceMutation extends Mutation
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $allowedRoles = [User::ROLE_ADMIN];
        try{
            AuthUtils::checkAuthenticationAndRoles($allowedRoles);
        } catch(Exception $e){
            return false;
        }
        return (bool) $this->auth;
    }

    protected $attributes = [
        'name' => 'service/removeService',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return Type::boolean();
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:services,id,deleted_at,NULL'],
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
        $service->delete();

        return true;
    }
}
