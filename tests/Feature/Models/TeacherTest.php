<?php

declare(strict_types=1);

use App\Models\Teacher;
use App\Models\Teaching;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

test('user relation')
    ->expect(fn () => Teacher::factory()->withUser()->create())
    ->user()->toBeInstanceOf(BelongsTo::class)
    ->user->toBeInstanceOf(User::class);

test('teachings relation')
    ->expect(fn () => Teacher::factory()->create())
    ->teachings()->toBeInstanceOf(HasMany::class)
    ->teachings->each->toBeInstanceOf(Teaching::class);
