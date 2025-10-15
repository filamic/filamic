<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\School;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
    }

    public function createSchools(): void
    {
        School::factory(10)->create();
    }
}
