<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Ticket;

use App\GraphQL\Validations\TicketValidation;
use App\Models\Ticket;
use App\Models\User;
use App\Utils\AuthUtils;
use Closure;
use Illuminate\Validation\ValidationException;
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
                'type' => Type::nonNull(Type::string()),
                'description' => 'O nome do requisitante'
            ],
            'client' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O nome do cliente (empresa)'
            ],
            'occupation_area' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O área que o requisitante opera e requer o suporte'
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $validator = TicketValidation::make($args);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            throw ValidationException::withMessages($errors);
        }

        $ticket = new Ticket();
        $ticket->fill($args);
        $ticket->save();

        return $ticket;
    }
}
