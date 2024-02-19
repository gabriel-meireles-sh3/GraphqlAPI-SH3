<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\User;

use App\Models\Support;
use App\Models\User;
use Closure;
use Error;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;;

class RegisterMutation extends Mutation
{
    protected $attributes = [
        'name' => 'user/Register',
        'description' => 'Mutation para o registro do usuário na aplicação'
    ];

    public function type(): Type
    {
        return GraphQL::type('User');
    }

    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required'],
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'email', 'unique:users'],
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'min:6', 'max:50'],
            ],
            'role' => [
                'name' => 'role',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'integer', 'in:1,2,3,4'],
            ],
            'service_area' => [
                'name' => 'service_area',
                'type' => Type::string(),
                'rule' => 'nullable',
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        DB::beginTransaction();

        $user = User::create([
            'name' => $args['name'],
            'role' => $args['role'],
            'email' => $args['email'],
            'password' => bcrypt($args['password']),
        ]);

        if ($args['role'] == User::ROLE_SUPPORT) {
            // Verifica se service_area está presente
            if (!array_key_exists('service_area', $args) || $args['service_area'] === null) {
                throw new \Exception('Field service_area required but not provided.');
            }
            // Cria a área de serviço para o usuário de suporte
            $newServiceArea = Support::create([
                'user_id' => $user->id,
                'service_area' => $args['service_area'],
            ]);

            if ($newServiceArea) {
                DB::commit();
                return $user;
            }else{
                throw new \Exception('Error creating the service area for the support user.');
            }
        }else if ($user) {
            DB::commit();
            return $user;
        }else{
            DB::rollBack();
            throw new \Exception('Create error');
        }
    }
}
