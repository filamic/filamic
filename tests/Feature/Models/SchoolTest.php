<?php

declare(strict_types=1);

use App\Models\Classroom;
use App\Models\School;
use App\Models\Subject;
use App\Models\SubjectCategory;
use Illuminate\Support\Carbon;

test('can mass assign fillable attributes', function () {
    // Arrange
    $attributes = [
        'name' => 'Test School',
        'address' => '123 Test St',
        'npsn' => '12345678',
        'nis_nss_nds' => '87654321',
        'telp' => '08123456789',
        'postal_code' => '12345',
        'village' => 'Test Village',
        'subdistrict' => 'Test Subdistrict',
        'city' => 'Test City',
        'province' => 'Test Province',
        'website' => 'https://test.com',
        'email' => 'test@school.com',
    ];

    // Act
    $school = School::create($attributes);

    // Assert
    expect($school)
        ->name->toBe('Test School')
        ->address->toBe('123 Test St')
        ->npsn->toBe('12345678')
        ->nis_nss_nds->toBe('87654321')
        ->telp->toBe('08123456789')
        ->postal_code->toBe('12345')
        ->village->toBe('Test Village')
        ->subdistrict->toBe('Test Subdistrict')
        ->city->toBe('Test City')
        ->province->toBe('Test Province')
        ->website->toBe('https://test.com')
        ->email->toBe('test@school.com');
});

test('id is guarded from mass assignment', function () {
    // Arrange & Act
    $school = School::create([
        'id' => 999,
        'name' => 'Test School',
    ]);

    // Assert
    expect($school->id)->not->toBe(999);
});

test('timestamps are automatically cast to Carbon instances', function () {
    // Arrange & Act
    $school = School::factory()->create();

    // Assert
    expect($school)
        ->created_at->toBeInstanceOf(Carbon::class)
        ->updated_at->toBeInstanceOf(Carbon::class);
});

test('has many relationship', function (string $relation, string $model, string $factory) {
    // Arrange
    $school = School::factory()->create();
    $records = $factory::factory()->count(3)->for($school)->create();

    // Act
    $result = $school->{$relation};

    // Assert
    expect($result)
        ->toHaveCount(3)
        ->each->toBeInstanceOf($model)
        ->and($result->pluck('id')->sort()->values()->toArray())
        ->toBe($records->pluck('id')->sort()->values()->toArray());
})->with([
    ['classrooms', Classroom::class, Classroom::class],
    ['subjectCategories', SubjectCategory::class, SubjectCategory::class],
]);

test('relationship returns empty collection when no records exist', function (string $relation) {
    // Arrange
    $school = School::factory()->create();

    // Act & Assert
    expect($school->{$relation})
        ->toBeEmpty()
        ->toBeInstanceOf(Illuminate\Database\Eloquent\Collection::class);
})->with(['classrooms', 'subjectCategories', 'subjects']);

test('can eager load relationship', function (string $relation, string $factory) {
    // Arrange
    $school = School::factory()->has($factory::factory()->count(2))->create();

    // Act
    $schoolWithRelation = School::with($relation)->find($school->id);

    // Assert
    expect($schoolWithRelation->relationLoaded($relation))->toBeTrue()
        ->and($schoolWithRelation->{$relation})->toHaveCount(2);
})->with([
    ['classrooms', Classroom::class],
    ['subjectCategories', SubjectCategory::class],
]);

test('has many subjects through subject categories', function () {
    // Arrange
    $school = School::factory()->create();
    [$category1, $category2] = SubjectCategory::factory(2)->for($school)->create();
    $subjects = collect([
        ...Subject::factory(2)->create(['subject_category_id' => $category1->id]),
        Subject::factory()->create(['subject_category_id' => $category2->id]),
    ]);

    // Act & Assert
    expect($school->subjects)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Subject::class)
        ->and($school->subjects->pluck('id')->sort()->values()->toArray())
        ->toBe($subjects->pluck('id')->sort()->values()->toArray());
});

test('can eager load subjects through subject categories', function () {
    // Arrange
    $school = School::factory()->create();
    $category = SubjectCategory::factory()->for($school)->create();
    Subject::factory(3)->create(['subject_category_id' => $category->id]);

    // Act & Assert
    $schoolWithSubjects = School::with('subjects')->find($school->id);
    expect($schoolWithSubjects->relationLoaded('subjects'))->toBeTrue()
        ->and($schoolWithSubjects->subjects)->toHaveCount(3);
});

test('subjects from different schools are not mixed', function () {
    // Arrange
    [$school1, $school2] = School::factory(2)->create();
    $category1 = SubjectCategory::factory()->for($school1)->create();
    $category2 = SubjectCategory::factory()->for($school2)->create();
    $subject1 = Subject::factory()->create(['subject_category_id' => $category1->id]);
    $subject2 = Subject::factory()->create(['subject_category_id' => $category2->id]);

    // Act & Assert
    expect($school1->subjects)
        ->toHaveCount(1)
        ->first()->id->toBe($subject1->id)
        ->and($school2->subjects)
        ->toHaveCount(1)
        ->first()->id->toBe($subject2->id);
});
