<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
    public function login(?User $user = null): void
    {
        $this->actingAs($user ?? User::factory()->create());
    }
}
