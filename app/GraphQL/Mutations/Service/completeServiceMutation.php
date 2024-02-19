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

class completeServiceMutation extends Mutation
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
        'name' => 'service/completeService',
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
            'service' => [
                'name' => 'service',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required',
            ],
        ];
    }

    public function validationErrorMessages(array $args = []): array
    {
        return [
            'service_id.exists' => 'Serviço não encontrado',
            'service.required' => 'Field Service não pode ser vazio'
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $user = auth()->user();
        $service = Service::find($args['service_id']);

        if (!$service) {
            throw new \Exception("Serviço não encontrado.");
        }
        
        $supports = $user->support;

        $matchingSupport = $supports->first(function ($support) use ($service) {
            return $support->id === $service->support_id;
        });

        if ($service->status === false && $matchingSupport) {
            $service->status = true;
            $service->service = $args['service'];

            $service->save();
            return $service;
        }

        throw new \Exception("Não é possível atualizar o serviço. Verifique o status e o suporte ID.");
    }
}
