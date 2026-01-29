<?php

declare(strict_types=1);

use App\Models\Classroom;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Teaching;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// test('teacher relation')
//     ->expect(fn () => Teaching::factory()->create())
//     ->teacher()->toBeInstanceOf(BelongsTo::class)
//     ->teacher->toBeInstanceOf(Teacher::class);

// test('subject relation')
//     ->expect(fn () => Teaching::factory()->create())
//     ->subject()->toBeInstanceOf(BelongsTo::class)
//     ->subject->toBeInstanceOf(Subject::class);

// test('classroom relation')
//     ->expect(fn () => Teaching::factory()->create())
//     ->classroom()->toBeInstanceOf(BelongsTo::class)
//     ->classroom->toBeInstanceOf(Classroom::class);

// test('schoolYear relation')
//     ->expect(fn () => Teaching::factory()->create())
//     ->schoolYear()->toBeInstanceOf(BelongsTo::class)
//     ->schoolYear->toBeInstanceOf(SchoolYear::class);
