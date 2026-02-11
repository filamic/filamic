<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Enums\InvoiceStatusEnum;
use App\Enums\InvoiceTypeEnum;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Invoice;
use App\Models\School;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

// describe('happy path', function () {
//     test('can create with valid attributes', function () {
//         // Arrange
//         $branch = Branch::factory()->create();
//         $school = School::factory()->for($branch)->create();
//         $classroom = Classroom::factory()->for($school)->create();
//         $schoolYear = SchoolYear::factory()->create();
//         $schoolTerm = SchoolTerm::factory()->create();
//         $student = Student::factory()->for($classroom)->create();

//         $attributes = [
//             'branch_id' => $branch->id,
//             'school_id' => $school->id,
//             'classroom_id' => $classroom->id,
//             'school_year_id' => $schoolYear->id,
//             'school_term_id' => $schoolTerm->id,
//             'student_id' => $student->id,
//             'type' => InvoiceTypeEnum::MONTHLY_FEE,
//             'amount' => 100000,
//             'fine' => 0,
//             'discount' => 10000,
//             'total_amount' => 90000,
//             'issued_at' => now(),
//             'due_date' => now()->addDays(7),
//             'status' => InvoiceStatusEnum::UNPAID,
//         ];

//         // Act
//         $invoice = Invoice::create($attributes);

//         // Assert
//         expect($invoice)->exists
//             ->branch_id->toBe($branch->id)
//             ->school_id->toBe($school->id)
//             ->student_id->toBe($student->id)
//             ->amount->toBe(100000)
//             ->status->toBe(InvoiceStatusEnum::UNPAID);
//     });

//     test('factory creates valid invoice', function () {
//         // Act
//         $invoice = Invoice::factory()->create();

//         // Assert - All required fields populated
//         expect($invoice)
//             ->id->not->toBeNull()
//             ->branch_id->not->toBeNull()
//             ->school_id->not->toBeNull()
//             ->student_id->not->toBeNull()
//             ->type->not->toBeNull()
//             ->amount->toBeNumeric()
//             ->status->not->toBeNull();
//     });

//     test('belongs to school relationship', function () {
//         // Arrange
//         $school = School::factory()->create();
//         $invoice = Invoice::factory()->for($school)->create();

//         // Act & Assert
//         expect($invoice->school)
//             ->toBeInstanceOf(School::class)
//             ->id->toBe($school->id);
//     });

//     test('belongs to student relationship', function () {
//         // Arrange
//         $student = Student::factory()->create();
//         $invoice = Invoice::factory()->for($student)->create();

//         // Act & Assert
//         expect($invoice->student)
//             ->toBeInstanceOf(Student::class)
//             ->id->toBe($student->id);
//     });

//     test('can eager load school relationship', function () {
//         // Arrange
//         $invoice = Invoice::factory()->create();

//         // Act
//         $loaded = Invoice::with('school')->find($invoice->id);

//         // Assert
//         expect($loaded->relationLoaded('school'))->toBeTrue()
//             ->and($loaded->school)->toBeInstanceOf(School::class);
//     });

//     test('can eager load student relationship', function () {
//         // Arrange
//         $invoice = Invoice::factory()->create();

//         // Act
//         $loaded = Invoice::with('student')->find($invoice->id);

//         // Assert
//         expect($loaded->relationLoaded('student'))->toBeTrue()
//             ->and($loaded->student)->toBeInstanceOf(Student::class);
//     });
// });

// describe('attribute assignment & casting', function () {
//     test('can mass assign fillable attributes', function () {
//         // Arrange
//         $branch = Branch::factory()->create();
//         $school = School::factory()->for($branch)->create();
//         $attributes = [
//             'branch_id' => $branch->id,
//             'school_id' => $school->id,
//             'classroom_id' => Classroom::factory()->for($school)->create()->id,
//             'school_year_id' => SchoolYear::factory()->create()->id,
//             'school_term_id' => SchoolTerm::factory()->create()->id,
//             'student_id' => Student::factory()->create()->id,
//             'type' => InvoiceTypeEnum::MONTHLY_FEE,
//             'amount' => 50000,
//             'fine' => 5000,
//             'discount' => 0,
//             'total_amount' => 55000,
//             'issued_at' => now(),
//             'due_date' => now()->addDays(7),
//             'status' => InvoiceStatusEnum::UNPAID,
//         ];

//         // Act
//         $invoice = Invoice::create($attributes);

//         // Assert
//         expect($invoice)
//             ->amount->toBe(50000)
//             ->fine->toBe(5000)
//             ->total_amount->toBe(55000);
//     });

//     test('id is guarded from mass assignment', function () {
//         // Act
//         $invoice = Invoice::create([
//             'id' => 'custom-id-123',
//             'branch_id' => Branch::factory()->create()->id,
//             'school_id' => School::factory()->create()->id,
//             'classroom_id' => Classroom::factory()->create()->id,
//             'school_year_id' => SchoolYear::factory()->create()->id,
//             'school_term_id' => SchoolTerm::factory()->create()->id,
//             'student_id' => Student::factory()->create()->id,
//             'type' => InvoiceTypeEnum::MONTHLY_FEE,
//             'amount' => 100000,
//             'fine' => 0,
//             'discount' => 0,
//             'total_amount' => 100000,
//             'issued_at' => now(),
//             'due_date' => now()->addDays(7),
//             'status' => InvoiceStatusEnum::UNPAID,
//         ]);

//         // Assert - Should use auto-generated ULID, not custom id
//         expect($invoice->id)->not->toBe('custom-id-123');
//     });

//     test('type is cast to InvoiceTypeEnum', function () {
//         // Act
//         $invoice = Invoice::factory()->create(['type' => 'MONTHLY_FEE']);

//         // Assert
//         expect($invoice->type)
//             ->toBe(InvoiceTypeEnum::MONTHLY_FEE)
//             ->toBeInstanceOf(InvoiceTypeEnum::class);
//     });

//     test('status is cast to InvoiceStatusEnum', function () {
//         // Act
//         $invoice = Invoice::factory()->create(['status' => 'UNPAID']);

//         // Assert
//         expect($invoice->status)
//             ->toBe(InvoiceStatusEnum::UNPAID)
//             ->toBeInstanceOf(InvoiceStatusEnum::class);
//     });

//     test('paid_at is cast to datetime', function () {
//         // Act
//         $invoice = Invoice::factory()->create(['paid_at' => '2024-01-15 10:30:00']);

//         // Assert
//         expect($invoice->paid_at)->toBeInstanceOf(Carbon::class);
//     });

//     test('timestamps are cast to Carbon instances', function () {
//         // Act
//         $invoice = Invoice::factory()->create();

//         // Assert
//         expect($invoice->created_at)->toBeInstanceOf(Carbon::class)
//             ->and($invoice->updated_at)->toBeInstanceOf(Carbon::class);
//     });
// });

// describe('relationship correctness', function () {
//     test('school relationship returns correct school instance', function () {
//         // Arrange
//         [$school1, $school2] = School::factory(2)->create();
//         $invoice1 = Invoice::factory()->for($school1)->create();
//         $invoice2 = Invoice::factory()->for($school2)->create();

//         // Act & Assert
//         expect($invoice1->school->id)->toBe($school1->id)
//             ->not->toBe($school2->id)
//             ->and($invoice2->school->id)->toBe($school2->id)
//             ->not->toBe($school1->id);
//     });

//     test('student relationship returns correct student instance', function () {
//         // Arrange
//         [$student1, $student2] = Student::factory(2)->create();
//         $invoice1 = Invoice::factory()->for($student1)->create();
//         $invoice2 = Invoice::factory()->for($student2)->create();

//         // Act & Assert
//         expect($invoice1->student->id)->toBe($student1->id)
//             ->not->toBe($student2->id)
//             ->and($invoice2->student->id)->toBe($student2->id)
//             ->not->toBe($student1->id);
//     });

//     test('can access nested relationships', function () {
//         // Arrange
//         $school = School::factory()->create();
//         $classroom = Classroom::factory()->for($school)->create();
//         $student = Student::factory()->for($classroom)->create();
//         $invoice = Invoice::factory()->for($student)->create();

//         // Act & Assert - Navigate through relationships
//         expect($invoice->student->classroom->school->id)->toBe($school->id);
//     });
// });

// describe('multi-tenancy', function () {
//     test('invoices from different schools are isolated', function () {
//         // Arrange
//         [$school1, $school2] = School::factory(2)->create();
//         $invoice1 = Invoice::factory()->for($school1)->create();
//         $invoice2 = Invoice::factory()->for($school2)->create();

//         // Act & Assert
//         expect($invoice1->school_id)->toBe($school1->id)
//             ->and($invoice2->school_id)->toBe($school2->id)
//             ->and($invoice1->school_id)->not->toBe($school2->id);
//     });

//     test('invoices from different branches are isolated', function () {
//         // Arrange
//         [$branch1, $branch2] = Branch::factory(2)->create();
//         $invoice1 = Invoice::factory()->for($branch1)->create();
//         $invoice2 = Invoice::factory()->for($branch2)->create();

//         // Act & Assert
//         expect($invoice1->branch_id)->toBe($branch1->id)
//             ->and($invoice2->branch_id)->toBe($branch2->id)
//             ->and($invoice1->branch_id)->not->toBe($branch2->id);
//     });

//     test('student invoices maintain school context', function () {
//         // Arrange
//         [$school1, $school2] = School::factory(2)->create();
//         $classroom1 = Classroom::factory()->for($school1)->create();
//         $classroom2 = Classroom::factory()->for($school2)->create();
//         $student1 = Student::factory()->for($classroom1)->create();
//         $student2 = Student::factory()->for($classroom2)->create();
//         $invoice1 = Invoice::factory()->for($student1)->for($school1)->create();
//         $invoice2 = Invoice::factory()->for($student2)->for($school2)->create();

//         // Act & Assert
//         expect($invoice1->school->id)->toBe($school1->id)
//             ->and($invoice2->school->id)->toBe($school2->id);
//     });
// });

// describe('scopes', function () {
//     test('paid scope returns only paid invoices', function () {
//         // Arrange
//         $student = Student::factory()->create();
//         Invoice::factory()->for($student)->create([
//             'status' => InvoiceStatusEnum::PAID,
//             'paid_at' => now(),
//         ]);
//         Invoice::factory(2)->for($student)->create([
//             'status' => InvoiceStatusEnum::UNPAID,
//             'paid_at' => null,
//         ]);

//         // Act
//         $paid = Invoice::paid()->get();

//         // Assert
//         expect($paid)->toHaveCount(1)
//             ->first()->status->toBe(InvoiceStatusEnum::PAID);
//     });

//     test('unpaid scope returns only unpaid invoices', function () {
//         // Arrange
//         $student = Student::factory()->create();
//         Invoice::factory()->for($student)->create([
//             'status' => InvoiceStatusEnum::PAID,
//             'paid_at' => now(),
//         ]);
//         Invoice::factory(2)->for($student)->create([
//             'status' => InvoiceStatusEnum::UNPAID,
//             'paid_at' => null,
//         ]);

//         // Act
//         $unpaid = Invoice::unpaid()->get();

//         // Assert
//         expect($unpaid)->toHaveCount(2)
//             ->each(fn ($invoice) => expect($invoice->status)->toBe(InvoiceStatusEnum::UNPAID));
//     });

//     test('monthly_fee scope returns only monthly fees', function () {
//         // Arrange
//         $student = Student::factory()->create();
//         Invoice::factory()->for($student)->create(['type' => InvoiceTypeEnum::MONTHLY_FEE]);
//         Invoice::factory(2)->for($student)->create(['type' => InvoiceTypeEnum::BOOK_FEE]);

//         // Act
//         $monthly = Invoice::monthlyFee()->get();

//         // Assert
//         expect($monthly)->toHaveCount(1)
//             ->first()->type->toBe(InvoiceTypeEnum::MONTHLY_FEE);
//     });

//     test('book_fee scope returns only book fees', function () {
//         // Arrange
//         $student = Student::factory()->create();
//         Invoice::factory(2)->for($student)->create(['type' => InvoiceTypeEnum::MONTHLY_FEE]);
//         Invoice::factory()->for($student)->create(['type' => InvoiceTypeEnum::BOOK_FEE]);

//         // Act
//         $bookFee = Invoice::bookFee()->get();

//         // Assert
//         expect($bookFee)->toHaveCount(1)
//             ->first()->type->toBe(InvoiceTypeEnum::BOOK_FEE);
//     });

//     test('scopes can be chained correctly', function () {
//         // Arrange
//         $student = Student::factory()->create();
//         // Paid monthly fee
//         Invoice::factory()->for($student)->create([
//             'status' => InvoiceStatusEnum::PAID,
//             'paid_at' => now(),
//             'type' => InvoiceTypeEnum::MONTHLY_FEE,
//         ]);
//         // Unpaid monthly fee
//         Invoice::factory()->for($student)->create([
//             'status' => InvoiceStatusEnum::UNPAID,
//             'paid_at' => null,
//             'type' => InvoiceTypeEnum::MONTHLY_FEE,
//         ]);
//         // Paid book fee
//         Invoice::factory()->for($student)->create([
//             'status' => InvoiceStatusEnum::PAID,
//             'paid_at' => now(),
//             'type' => InvoiceTypeEnum::BOOK_FEE,
//         ]);

//         // Act - Chain scopes
//         $paidMonthly = Invoice::paidMonthlyFee()->get();
//         $unpaidMonthly = Invoice::unpaidMonthlyFee()->get();

//         // Assert
//         expect($paidMonthly)->toHaveCount(1)
//             ->first()->status->toBe(InvoiceStatusEnum::PAID)
//             ->type->toBe(InvoiceTypeEnum::MONTHLY_FEE)
//             ->and($unpaidMonthly)->toHaveCount(1)
//             ->first()->status->toBe(InvoiceStatusEnum::UNPAID)
//             ->type->toBe(InvoiceTypeEnum::MONTHLY_FEE);
//     });

//     test('active_year scope returns invoices for active year only', function () {
//         // Arrange
//         $activeYear = SchoolYear::factory()->create(['is_active' => true]);
//         $inactiveYear = SchoolYear::factory()->create(['is_active' => false]);
//         $student = Student::factory()->create();

//         Invoice::factory(2)->for($student)->for($activeYear)->create();
//         Invoice::factory(2)->for($student)->for($inactiveYear)->create();

//         // Act
//         $active = Invoice::activeYear()->get();

//         // Assert
//         expect($active)->toHaveCount(2)
//             ->each(fn ($inv) => expect($inv->school_year_id)->toBe($activeYear->id));
//     });

//     test('active_term scope returns invoices for active term only', function () {
//         // Arrange
//         $activeTerm = SchoolTerm::factory()->create(['is_active' => true]);
//         $inactiveTerm = SchoolTerm::factory()->create(['is_active' => false]);
//         $student = Student::factory()->create();

//         Invoice::factory(2)->for($student)->for($activeTerm)->create();
//         Invoice::factory(2)->for($student)->for($inactiveTerm)->create();

//         // Act
//         $active = Invoice::activeTerm()->get();

//         // Assert
//         expect($active)->toHaveCount(2)
//             ->each(fn ($inv) => expect($inv->school_term_id)->toBe($activeTerm->id));
//     });
// });

// describe('domain rules', function () {
//     test('generate fingerprint throws exception if student_id missing', function () {
//         // Act & Assert
//         $this->expectException(InvalidArgumentException::class);
//         Invoice::generateFingerprint([
//             'type' => InvoiceTypeEnum::MONTHLY_FEE,
//             'school_year_id' => 'year-123',
//             // missing student_id
//         ]);
//     });

//     test('generate fingerprint throws exception if school_year_id missing', function () {
//         // Act & Assert
//         $this->expectException(InvalidArgumentException::class);
//         Invoice::generateFingerprint([
//             'type' => InvoiceTypeEnum::MONTHLY_FEE,
//             'student_id' => 'student-123',
//             // missing school_year_id
//         ]);
//     });

//     test('generate fingerprint returns correct format with month', function () {
//         // Act
//         $fingerprint = Invoice::generateFingerprint([
//             'type' => InvoiceTypeEnum::MONTHLY_FEE,
//             'student_id' => 'student-123',
//             'school_year_id' => 'year-456',
//             'month' => 5,
//         ]);

//         // Assert
//         expect($fingerprint)->toBe('MONTHLY_FEE_student-123_year-456_5');
//     });

//     test('generate fingerprint uses annual when month is null', function () {
//         // Act
//         $fingerprint = Invoice::generateFingerprint([
//             'type' => InvoiceTypeEnum::BOOK_FEE,
//             'student_id' => 'student-123',
//             'school_year_id' => 'year-456',
//             'month' => null,
//         ]);

//         // Assert
//         expect($fingerprint)->toBe('BOOK_FEE_student-123_year-456_annual');
//     });

//     test('generate reference number has correct format', function () {
//         // Act
//         $reference = Invoice::generateReferenceNumber();

//         // Assert
//         expect($reference)->toMatch('/^INV\/\d{8}\/.{6}$/');
//     });
// });

// describe('model events', function () {
//     test('generates fingerprint on create', function () {
//         // Arrange
//         $data = [
//             'branch_id' => Branch::factory()->create()->id,
//             'school_id' => School::factory()->create()->id,
//             'classroom_id' => Classroom::factory()->create()->id,
//             'school_year_id' => SchoolYear::factory()->create()->id,
//             'school_term_id' => SchoolTerm::factory()->create()->id,
//             'student_id' => Student::factory()->create()->id,
//             'type' => InvoiceTypeEnum::MONTHLY_FEE,
//             'amount' => 100000,
//             'fine' => 0,
//             'discount' => 0,
//             'total_amount' => 100000,
//             'issued_at' => now(),
//             'due_date' => now()->addDays(7),
//             'status' => InvoiceStatusEnum::UNPAID,
//         ];

//         // Act
//         $invoice = Invoice::create($data);

//         // Assert
//         expect($invoice->fingerprint)->not->toBeEmpty()
//             ->and($invoice->fingerprint)->toContain('MONTHLY_FEE');
//     });

//     test('generates reference number on create', function () {
//         // Act
//         $invoice = Invoice::factory()->create();

//         // Assert
//         expect($invoice->reference_number)
//             ->toContain('INV/')
//             ->and($invoice->reference_number)->toMatch('/^INV\/\d{8}\/.{6}$/');
//     });

//     test('fingerprint is unique per student per year per type', function () {
//         // Arrange
//         $student = Student::factory()->create();
//         $schoolYear = SchoolYear::factory()->create();

//         // Act
//         $invoice1 = Invoice::factory()->for($student)->for($schoolYear)->create([
//             'type' => InvoiceTypeEnum::MONTHLY_FEE,
//         ]);
//         $invoice2 = Invoice::factory()->for($student)->for($schoolYear)->create([
//             'type' => InvoiceTypeEnum::MONTHLY_FEE,
//         ]);

//         // Assert - Same student/year/type should generate same fingerprint
//         expect($invoice1->fingerprint)->toBe($invoice2->fingerprint);
//     });
// });

// describe('computed attributes', function () {
//     test('formatted_amount returns currency format', function () {
//         // Act
//         $invoice = Invoice::factory()->create(['amount' => 50000]);

//         // Assert
//         expect($invoice->formatted_amount)
//             ->toContain('Rp.')
//             ->and($invoice->formatted_amount)->toContain('50');
//     });

//     test('formatted_amount handles decimal amounts', function () {
//         // Act
//         $invoice = Invoice::factory()->create(['amount' => 50000.50]);

//         // Assert
//         expect($invoice->formatted_amount)->toContain('Rp.');
//     });
// });

// describe('edge cases', function () {
//     test('can update invoice attributes', function () {
//         // Arrange
//         $invoice = Invoice::factory()->create();

//         // Act
//         $invoice->update([
//             'amount' => 150000,
//             'status' => InvoiceStatusEnum::PAID,
//             'paid_at' => now(),
//         ]);

//         // Assert
//         expect($invoice)
//             ->amount->toBe(150000)
//             ->status->toBe(InvoiceStatusEnum::PAID)
//             ->paid_at->not->toBeNull();
//     });

//     test('factory can override attributes', function () {
//         // Arrange
//         $school = School::factory()->create();

//         // Act
//         $invoice = Invoice::factory()->create([
//             'school_id' => $school->id,
//             'amount' => 999999,
//         ]);

//         // Assert
//         expect($invoice)
//             ->school_id->toBe($school->id)
//             ->amount->toBe(999999);
//     });

//     test('factory creates multiple records', function () {
//         // Act
//         $invoices = Invoice::factory(5)->create();

//         // Assert
//         expect($invoices)->toHaveCount(5)
//             ->each->toBeInstanceOf(Invoice::class);
//     });

//     test('can handle zero discount', function () {
//         // Act
//         $invoice = Invoice::factory()->create(['discount' => 0]);

//         // Assert
//         expect($invoice->discount)->toBe(0);
//     });

//     test('can handle zero fine', function () {
//         // Act
//         $invoice = Invoice::factory()->create(['fine' => 0]);

//         // Assert
//         expect($invoice->fine)->toBe(0);
//     });

//     test('can handle null month for annual fees', function () {
//         // Act
//         $invoice = Invoice::factory()->create([
//             'type' => InvoiceTypeEnum::BOOK_FEE,
//             'month' => null,
//         ]);

//         // Assert
//         expect($invoice->month)->toBeNull();
//     });
// });
