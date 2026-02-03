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

            $table->foreignUlid('branch_id')->constrained();
            $table->foreignUlid('school_id')->constrained();
            $table->foreignUlid('classroom_id')->constrained();
            $table->foreignUlid('school_year_id')->constrained();
            $table->foreignUlid('school_term_id')->constrained();
            $table->foreignUlid('student_id')->constrained();

            $table->string('reference_number')->unique();
            $table->string('fingerprint')->unique();

            $table->string('branch_name');
            $table->string('school_name');
            $table->string('classroom_name');
            $table->string('school_year_name');
            $table->string('school_term_name');
            $table->string('student_name');
            $table->string('virtual_account_number');

            $table->unsignedTinyInteger('type');
            $table->unsignedTinyInteger('month_id')->nullable();

            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('fine', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);

            $table->unsignedTinyInteger('status')->default(1)->index();
            $table->unsignedTinyInteger('payment_method')->nullable();
            $table->dateTime('paid_at')->nullable();

            $table->date('due_date');
            $table->date('issued_at');

            $table->longText('description')->nullable();

            $table->timestamps();
        });
    }
};
