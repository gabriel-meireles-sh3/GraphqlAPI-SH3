<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Ticket;

use App\Models\Ticket;
use App\Models\User;
use App\Utils\AuthUtils;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class updateTicketMutation extends Mutation
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
        'name' => 'ticket/updateTicket',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Ticket');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:tickets,id,deleted_at,NULL'],
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required'],
            ],
            'client' => [
                'name' => 'client',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required'],
            ],
            'occupation_area' => [
                'name' => 'occupation_area',
                'type' => Type::nonNull(Type::string()),
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
        $ticket = Ticket::findOrFail($args['id']);
        $ticket->update($args);

        return $ticket;
    }
}
