<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Service;

use App\Models\Service;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ServicesByTicketIdQuery extends Query
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        try{
            $this->auth = JWTAuth::parseToken()->authenticate();
        } catch(JWTException $e){
            return false;
        }
        return (bool) $this->auth;
    }

    protected $attributes = [
        'name' => 'service/ServicesByTicketId',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Service'));
    }

    public function args(): array
    {
        return [
            'ticket_id' => [
                'name' => 'ticket_id',
                'type' => Type::string(),
                'rules' => ['required', 'exists:tickets,id,deleted_at,NULL'],
                'description' => 'O ID dentro do banco tickets'
            ]
        ];
    }

    public function validationErrorMessages(array $args = []): array
    {
        return [
            'ticket_id.exists' => 'Ticket ID não encontrado',
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $services = Service::where('client_id', $args['ticket_id'])->get();

        if ($services->isEmpty()){
            return null;
        }

        return $services;
    }
}
