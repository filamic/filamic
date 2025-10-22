<?php

declare(strict_types=1);

namespace Tests;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Context;

abstract class TestCase extends BaseTestCase
{
    public function login(?User $user = null): void
    {
        $this->actingAs($user ?? User::factory()->create());
    }

    public function loginAdmin(?User $user = null): void
    {
        $school = School::factory()->create();

        Context::add('school', $school);

        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('mantapjiwa00'),
        ]);

        $this->login($user);
    }
}
