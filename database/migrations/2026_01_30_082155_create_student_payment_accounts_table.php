<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_payment_accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedBigInteger('legacy_old_id')->nullable();
            $table->foreignUlid('school_id')->constrained();
            $table->foreignUlid('student_id')->constrained();
            $table->string('monthly_fee_virtual_account')->nullable()->unique();
            $table->string('book_fee_virtual_account')->nullable()->unique();
            $table->unsignedInteger('monthly_fee_amount')->default(0);
            $table->unsignedInteger('book_fee_amount')->default(0);

            $table->timestamps();

            $table->unique(['school_id', 'student_id']);
        });
    }
};
