<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('student_enrollment_id')->constrained();
            $table->foreignUlid('student_payment_account_id')->constrained();

            $table->string('school_name');
            $table->string('classroom_name');
            $table->string('school_year_name');
            $table->string('student_name');
            $table->string('virtual_account_number');

            $table->unsignedTinyInteger('type');
            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('fine', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->unsignedTinyInteger('month_id')->nullable();

            $table->unsignedTinyInteger('payment_method')->nullable();
            $table->boolean('is_paid')->default(false)->index();
            $table->dateTime('paid_at')->nullable();

            $table->date('start_date');
            $table->date('end_date');
            $table->longText('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['student_enrollment_id', 'type', 'month_id']);
        });
    }
};
