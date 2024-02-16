<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Ticket;

use App\GraphQL\Validations\TicketValidation;
use App\Models\Ticket;
use App\Models\User;
use App\Utils\AuthUtils;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class updateTicketMutation extends Mutation
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $allowedRoles = [User::ROLE_ADMIN];
        try{
            $this->auth = AuthUtils::checkAuthenticationAndRoles($allowedRoles);
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
                'type' => Type::int(),
                'rules' => ['required', 'exists:tickets,id,deleted_at,NULL'],
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'O nome do requisitante'
            ],
            'client' => [
                'type' => Type::string(),
                'description' => 'O nome do cliente (empresa)'
            ],
            'occupation_area' => [
                'type' => Type::string(),
                'description' => 'O área que o requisitante opera e requer o suporte'
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

        $validator = TicketValidation::make($args);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            throw ValidationException::withMessages($errors);
        }

        $ticket->update($args);

        $ticket = $ticket->fresh();

        return $ticket;
    }
}
