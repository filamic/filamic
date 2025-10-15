<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Support\Carbon;

test('fillable', function () {
    expect(User::factory()->make()->toArray())
        ->toHaveKeys([
            'name',
            'email',
        ]);
});

test('hidden', function () {
    expect(
        User::factory()
            ->create([
                'password' => 'password',
                'remember_token' => 'token',
            ])
            ->toArray()
    )
        ->not->toHaveKeys([
            'password',
            'remember_token',
        ]);
});

test('casts', function () {
    expect(
        User::factory()
            ->create([
                'password' => 'password',
            ])
    )
        ->email_verified_at->toBeInstanceOf(Carbon::class)
        ->password->toStartWith('$2y');
});
