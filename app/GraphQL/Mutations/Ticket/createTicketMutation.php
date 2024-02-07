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

class createTicketMutation extends Mutation
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $allowedRoles = [User::ROLE_ADMIN, User::ROLE_ATTENDANT];
        try {
            $this->auth = AuthUtils::checkAuthenticationAndRoles($allowedRoles);
        } catch (Exception $e) {
            return false;
        }
        return (bool) $this->auth;
    }

    protected $attributes = [
        'name' => 'ticket/createTicket',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Ticket');
    }

    public function args(): array
    {
        return [
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

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /*$ticket = Ticket::create([
            'name' => $args['name'],
            'client' => $args['client'],
            'occupation_area' => $args['occupation_area'],
        ]);*/

        $ticket = new Ticket();
        $ticket->fill($args);
        $ticket->save();

        return $ticket;
    }
}
