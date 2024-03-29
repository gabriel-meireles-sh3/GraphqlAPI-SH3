<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Service;
use App\Models\ServiceAreas;
use App\Models\Support;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'email' => "teste@gmail.com",
            'password' => "123456",
            'role' => 1,
        ]);
        User::factory(10)->create();
        $user = User::factory()->create(['role' => User::ROLE_SUPPORT]);
        $serviceArea = Support::factory()->create(['user_id' => $user->id]);

        Support::factory(10)->create();
        Ticket::factory(10)->create();

        Service::factory(10)->create();
        Service::factory()->create(['support_id' => NULL]);
        Service::factory()->create(['status' => true]);
        Service::factory()->create(['service_area' => $serviceArea->service_area]);
        Service::factory()->create(['support_id' => $serviceArea->id]);
    }
}
