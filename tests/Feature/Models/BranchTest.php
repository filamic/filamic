<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\Classroom;
use App\Models\School;
use App\Models\Student;
use App\Models\User;

test('it prevents mass assignment to guarded id', function () {
    // ARRANGE
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

    // ACT
    $branch = Branch::create([
        'id' => $customId,
        'name' => 'Alpha Branch',
    ]);

    // ASSERT
    expect($branch->getKey())
        ->toBeString()
        ->not->toBe($customId);
});

test('it isolates schools by branch', function () {
    // ARRANGE
    $branch = Branch::factory()->create();
    $otherBranch = Branch::factory()->create();

    $firstSchool = School::factory()->for($branch)->create();
    $secondSchool = School::factory()->for($branch)->create();

    School::factory()->for($otherBranch)->create();

    // ACT
    $schools = $branch->schools;

    // ASSERT
    expect($schools)
        ->toHaveCount(2)
        ->pluck('id')->toContain($firstSchool->getKey(), $secondSchool->getKey())
        ->and(
            $schools->pluck('branch_id')
                ->unique()
                ->all()
        )->toBe([$branch->getKey()]);
});

test('it manages users relation with attach sync and detach', function () {
    // ARRANGE
    $branch = Branch::factory()->create();
    $userOne = User::factory()->create();
    $userTwo = User::factory()->create();
    $userThree = User::factory()->create();

    // ACT
    $branch->users()->attach([$userOne->getKey(), $userTwo->getKey()]);
    $branch->users()->sync([$userOne->getKey(), $userThree->getKey()]);
    $branch->users()->detach($userThree->getKey());
    $branch->load('users');

    // ASSERT
    expect($branch->users)
        ->toHaveCount(1)
        ->first()->getKey()->toBe($userOne->getKey());
});

test('it does not mix users from different branches', function () {
    // ARRANGE
    $branch1 = Branch::factory()->create();
    $branch2 = Branch::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // ACT
    $branch1->users()->attach($user1->getKey());
    $branch2->users()->attach($user2->getKey());
    $branch1->load('users');
    $branch2->load('users');

    // ASSERT
    expect($branch1->users)
        ->toHaveCount(1)
        ->first()->getKey()->toBe($user1->getKey());

    expect($branch2->users)
        ->toHaveCount(1)
        ->first()->getKey()->toBe($user2->getKey());
});

test('it isolates classrooms through schools by branch', function () {
    // ARRANGE
    $branch = Branch::factory()->create();
    $otherBranch = Branch::factory()->create();

    $school = School::factory()->for($branch)->create();
    $otherSchool = School::factory()->for($otherBranch)->create();

    $classroom = Classroom::factory()->for($school)->create();
    Classroom::factory()->for($otherSchool)->create();

    // ACT
    $classrooms = $branch->classrooms;

    // ASSERT
    expect($classrooms)
        ->toHaveCount(1)
        ->first()->getKey()->toBe($classroom->getKey());
});

test('it isolates students by branch', function () {
    // ARRANGE
    $branch = Branch::factory()->create();
    $otherBranch = Branch::factory()->create();

    $student = Student::factory()->for($branch)->create();
    Student::factory()->for($otherBranch)->create();

    // ACT
    $students = $branch->students;

    // ASSERT
    expect($students)
        ->toHaveCount(1)
        ->first()->getKey()->toBe($student->getKey());
});
