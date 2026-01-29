<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('classroom_id')->constrained();
            $table->foreignUlid('user_id')->constrained();
            $table->foreignUlid('subject_id')->constrained();
            $table->foreignUlid('school_year_id')->constrained();
            $table->foreignUlid('school_term_id')->constrained();
            $table->timestamps();
        });

        // TODO: we need to use this later
        // $table->ulid('id')->primary();
        // $table->foreignUlid('school_year_id')->constrained();
        // $table->foreignUlid('classroom_id')->constrained();
        // $table->foreignUlid('employee_id')->constrained();
        // $table->foreignUlid('subject_id')->constrained();
        // $table->foreignUlid('group_id')->nullable()->constrained(); // Null jika kelas reguler
        // $table->timestamps();
    }
};
