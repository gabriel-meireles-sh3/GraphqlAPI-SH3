<?php

declare(strict_types=1);

namespace App\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class UserDataInput extends InputType
{
    protected $inputObject = true;

    protected $attributes = [
        'name' => 'UserData',
        'description' => 'An example input',
    ];

    public function fields(): array
    {
        return [
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The name of the user.',
            ],
            'role' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The role of the user.',
            ],
            'email' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The email of the user.',
            ],
            'password' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The password of the user.',
            ],
        ];
    }
}
