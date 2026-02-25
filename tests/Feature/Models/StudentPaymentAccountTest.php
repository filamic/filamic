<?php

declare(strict_types=1);

use App\Models\Student;
use App\Models\StudentPaymentAccount;

it('returns accounts eligible for monthly fee', function () {
    // Arrange
    $student = Student::factory()->create();
    $eligible = StudentPaymentAccount::factory()->for($student)->create([
        'monthly_fee_virtual_account' => '12345678',
        'monthly_fee_amount' => 150_000,
    ]);
    StudentPaymentAccount::factory()->for(Student::factory())->create([
        'monthly_fee_virtual_account' => null,
        'monthly_fee_amount' => 0,
    ]);

    // Act
    $result = StudentPaymentAccount::eligibleForMonthlyFee()->get();

    // Assert
    expect($result)
        ->toHaveCount(1)
        ->first()->id->toBe($eligible->id);
});

it('excludes accounts with null monthly fee virtual account', function () {
    // Arrange
    StudentPaymentAccount::factory()->create([
        'monthly_fee_virtual_account' => null,
        'monthly_fee_amount' => 150_000,
    ]);

    // Act
    $result = StudentPaymentAccount::eligibleForMonthlyFee()->get();

    // Assert
    expect($result)->toHaveCount(0);
});

it('excludes accounts with zero monthly fee amount', function () {
    // Arrange
    StudentPaymentAccount::factory()->create([
        'monthly_fee_virtual_account' => '12345678',
        'monthly_fee_amount' => 0,
    ]);

    // Act
    $result = StudentPaymentAccount::eligibleForMonthlyFee()->get();

    // Assert
    expect($result)->toHaveCount(0);
});

it('returns accounts eligible for book fee', function () {
    // Arrange
    $student = Student::factory()->create();
    $eligible = StudentPaymentAccount::factory()->for($student)->create([
        'book_fee_virtual_account' => '87654321',
        'book_fee_amount' => 100_000,
    ]);
    StudentPaymentAccount::factory()->for(Student::factory())->create([
        'book_fee_virtual_account' => null,
        'book_fee_amount' => 0,
    ]);

    // Act
    $result = StudentPaymentAccount::eligibleForBookFee()->get();

    // Assert
    expect($result)
        ->toHaveCount(1)
        ->first()->id->toBe($eligible->id);
});

it('excludes accounts with null book fee virtual account', function () {
    // Arrange
    StudentPaymentAccount::factory()->create([
        'book_fee_virtual_account' => null,
        'book_fee_amount' => 100_000,
    ]);

    // Act
    $result = StudentPaymentAccount::eligibleForBookFee()->get();

    // Assert
    expect($result)->toHaveCount(0);
});

it('excludes accounts with zero book fee amount', function () {
    // Arrange
    StudentPaymentAccount::factory()->create([
        'book_fee_virtual_account' => '87654321',
        'book_fee_amount' => 0,
    ]);

    // Act
    $result = StudentPaymentAccount::eligibleForBookFee()->get();

    // Assert
    expect($result)->toHaveCount(0);
});
