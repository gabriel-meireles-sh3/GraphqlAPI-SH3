<?php

namespace Tests\Feature;

use App\Models\ServiceAreas;
use App\Models\Support;
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

    public function test_mutation_cadastroUsuario_support()
    {
        $userData = [
            'name' => $this->faker->name,
            'role' => "3",
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'service_area' => 'service,'
        ];

        $this->mutation('register', [
            'name' => $userData['name'],
            'role' => $userData['role'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'service_area' => $userData['service_area'],
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

    public function test_mutation_login_withValidData()
    {
        $user = User::factory()->create([
            'email' => "teste@gmail.com",
            'password' => "123456",
        ]);

        $response = $this->mutation('login', [
            'email' => "teste@gmail.com",
            'password' => "123456",
        ], [])
        ->assertJson([
            'data' => [
                "login" => true,
            ],
        ]);
    }
    
    public function test_mutation_login_withInvalidData()
    {
        $invalidLoginData = [
            'email' => 'nonexistentuser@example.com',
            'password' => 'invalidpassword',
        ];
        $this->mutation('login', [
            'email' => $invalidLoginData['email'],
            'password' => $invalidLoginData['password'],
        ], [])
            ->assertJson([
                'data' => [
                    'login' => null,
                ],
                'errors' => true,
            ]);
    }

    public function teste_mutation_logout()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $this->withHeaders([
            "Authorization" => "Bearer {$token}"
        ])->mutation('logout', [], [])
        ->assertJson([
            'data' => [
                'logout' => true,
            ]
        ]);
    }

    public function teste_query_users()
    {
        $user = User::factory()->create();
        $token  = auth()->login($user);

        $users = User::factory(3)->create(['name' => 'teste']);

        $expectedUsers = $users->map(function ($users) {
            return [
                'id' => $users->id,
                'name' => $users->name,
                'role' => $users->role,
                'email' => $users->email,
            ];
        })->toArray();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('users', ['name' => 'teste'], ['id', 'name', 'role', 'email'])
            ->assertJson([
                'data' => [
                    "users" => $expectedUsers,
                ],
            ]);
    }

    public function teste_query_user()
    {
        $user = User::factory()->create();
        $token  = auth()->login($user);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('user', ['id' => $user->id], ['id', 'name', 'role', 'email'])
            ->assertJson([
                'data' => [
                    "user" => true,
                ],
            ]);
    }

    public function teste_query_AllSupport()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $supportUser = User::factory()->create(['role' => '3']);
        $this->assertNotNull($supportUser->id);
        
        $service_area = Support::factory()->create(['user_id' => $supportUser->id]);
        $this->assertNotNull($service_area->user_id);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('allSuport', ['id', 'name', 'role', 'service_areas' => ['service_area']])
            ->assertJson([
                'data' => [
                    "allSuport" => true,
                ],
            ]);
    }
}
