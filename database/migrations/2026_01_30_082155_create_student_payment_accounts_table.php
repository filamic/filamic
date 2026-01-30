<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_payment_accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('school_id')->constrained();
            $table->foreignUlid('student_id')->constrained();
            $table->string('monthly_fee_virtual_account')->nullable()->unique();
            $table->string('book_fee_virtual_account')->nullable()->unique();
            $table->decimal('monthly_fee_amount', 12, 2)->unsigned()->default(0); 
            $table->decimal('book_fee_amount', 12, 2)->unsigned()->default(0);

            $table->timestamps();

            $table->unique(['school_id', 'student_id']);
        });
    }
};
