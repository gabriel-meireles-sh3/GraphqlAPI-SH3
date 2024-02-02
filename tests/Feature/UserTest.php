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
    public function test_mutation_cadastroUsuario_withValidData()
    {
        $userData = [
            'name' => $this->faker->name,
            'role' => "4",
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ];

        $response = $this->mutation('register', [
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

        //var_dump($response);
    }

    public function test_mutation_cadastroUsuario_withInvalidData()
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

    /*
    public function test_mutation_login_withValidData()
    {
        $user = User::factory()->create([
            'email' => "teste@gmail.com",
            'password' => "123456",
        ]);

        $response = $this->mutation('login', [
            'email' => "teste@gmail.com",
            'password' => "123456",
        ]);

        var_dump($response->json());
    }*/
    /*
    public function test_mutation_login_withInvalidData()
    {
        $invalidLoginData = [
            'email' => 'nonexistentuser@example.com',
            'password' => 'invalidpassword',
        ];
        $this->mutation('login', [
            'email' => $invalidLoginData['email'],
            'password' => $invalidLoginData['password'],
        ],)
            ->assertJson([
                'data' => [
                    'login' => null,
                ],
                'errors' => true,
            ]);
    }*/

    public function teste_mutation_logout()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeaders([
            "Authorization" => "Bearer {$token}"
        ])->mutation('logout');
        
    }
}
