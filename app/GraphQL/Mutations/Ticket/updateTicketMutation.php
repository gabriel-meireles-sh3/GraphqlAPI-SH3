<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Ticket;

use App\Models\Ticket;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class updateTicketMutation extends Mutation
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        try {
            $this->auth = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
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
