<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    public function test_mutation_cadastroUsuario_WithValidData()
    {
        $userData = [
            'name' => $this->faker->name,
            'role' => "4",
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ];

        $this->mutation('register', [
            'name' => $userData['name'],
            'role' => $userData['role'],
            'email' => $userData['email'],
            'password' => $userData['password'],
        ], ['id', 'name', 'role', 'email'])
            ->assertJson([
                'data' => [
                    'register' => [
                        'name' => $userData['name'],
                        'role' => $userData['role'],
                        'email' => $userData['email'],
                    ],
                ],
            ]);
    }

    public function test_mutation_cadastroUsuario_WithInvalidData()
    {
        $userData = [
            'name' => $this->faker->name,
            'role' => "5",
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ];

        $this->mutation('register', [
            'name' => $userData['name'],
            'role' => $userData['role'],
            'email' => $userData['email'],
            'password' => $userData['password'],
        ], ['id', 'name', 'role', 'email'])
            ->assertJson([
                'data' => [
                    'register' => null,
                ],
            ]);
    }

    public function test_mutation_login_WithValidData()
    {
        User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('password123'),
        ]);
        $loginData = [
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ];

        $this->mutation('login', $loginData, ['token'])
            ->assertJsonStructure([
                'data' => [
                    'login' => [
                        'token',
                    ],
                ],
            ]);
    }

    public function test_mutation_login_WithInvalidData()
    {
        $invalidLoginData = [
            'email' => 'nonexistentuser@example.com',
            'password' => 'invalidpassword',
        ];
        $this->mutation('login', [
            'email' => $invalidLoginData['email'],
            'password' => $invalidLoginData['passowrd'],
        ],)
            ->assertJson([
                'data' => [
                    'login' => null,
                ],
                'errors' => true,
            ]);
    }
}
