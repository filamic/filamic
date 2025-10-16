<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Classroom;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('mantapjiwa00'),
        ]);

        $this->createSchools();
        $this->createClassrooms();
    }

    public function createSchools(): void
    {
        School::factory(10)->create();
    }

    public function createClassrooms(): void
    {
        Classroom::factory(10)->create();
    }
}
