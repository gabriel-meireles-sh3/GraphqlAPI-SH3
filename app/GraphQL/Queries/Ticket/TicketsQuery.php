<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Ticket;

use App\Models\Service;
use App\Models\Ticket;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class TicketsQuery extends Query
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
        'name' => 'ticket/Tickets',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Ticket'));
    }

    public function args(): array
    {
        return [

        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $fields)
    {
        // Obtenha os campos e relações selecionados
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $tickets = Ticket::select($select)->with($with)->get();

        return $tickets;
    }
}
