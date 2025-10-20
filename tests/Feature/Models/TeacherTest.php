<?php

declare(strict_types=1);

use App\Models\Teacher;
use App\Models\Teaching;
use Illuminate\Database\Eloquent\Relations\HasMany;

test('teachings relation')
    ->expect(fn () => Teacher::factory()->create())
    ->teachings()->toBeInstanceOf(HasMany::class)
    ->teachings->each->toBeInstanceOf(Teaching::class);
