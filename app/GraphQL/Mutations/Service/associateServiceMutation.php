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
use phpDocumentor\Reflection\Types\Null_;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class associateServiceMutation extends Mutation
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $allowedRoles = [User::ROLE_SUPPORT];
        try{
            $this->auth = AuthUtils::checkAuthenticationAndRoles($allowedRoles);
        } catch(Exception $e){
            return false;
        }
        return (bool) $this->auth;
    }

    protected $attributes = [
        'name' => 'service/associateService',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Service');
    }

    public function args(): array
    {
        return [
            'service_id' => [
                'name' => 'service_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:services,id,deleted_at,NULL'],
            ],
        ];
    }

    public function validationErrorMessages(array $args = []): array
    {
        return [
            'service_id.exists' => 'Serviço não encontrado',
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $user = auth()->user();
        $service = Service::findOrFail($args['service_id']);

        $supports = $user->support;

        $matchingSupport = $supports->first(function ($support) use ($service) {
            return $support->service_area === $service->service_area;
        });

        if ($service->support_id === null && $matchingSupport) {
            $service->support_id = $matchingSupport->id;
            $service->save();

            return $service;
        }

        throw new \Exception('There is already an analyst responding to this service or the service area does not match any support.');
    }
}
