<?php

declare(strict_types=1);

use App\Models\Classroom;
use App\Models\HomeroomClass;
use App\Models\SchoolYear;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('teacher relation')
    ->expect(fn () => HomeroomClass::factory()->create())
    ->teacher()->toBeInstanceOf(BelongsTo::class)
    ->teacher->toBeInstanceOf(Teacher::class);

test('classroom relation')
    ->expect(fn () => HomeroomClass::factory()->create())
    ->classroom()->toBeInstanceOf(BelongsTo::class)
    ->classroom->toBeInstanceOf(Classroom::class);

test('schoolYear relation')
    ->expect(fn () => HomeroomClass::factory()->create())
    ->schoolYear()->toBeInstanceOf(BelongsTo::class)
    ->schoolYear->toBeInstanceOf(SchoolYear::class);
