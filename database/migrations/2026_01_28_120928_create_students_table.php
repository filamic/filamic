<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->index();
            $table->foreignUlid('school_id')->nullable()->constrained();
            $table->foreignUlid('user_id')->nullable()->constrained();
            $table->foreignUlid('father_id')->nullable()->constrained('users');
            $table->foreignUlid('mother_id')->nullable()->constrained('users');
            $table->foreignUlid('guardian_id')->nullable()->constrained('users');
            $table->string('nisn')->unique()->nullable();
            $table->string('nis')->nullable()->index();
            $table->unsignedTinyInteger('gender');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('previous_education')->nullable();
            $table->string('joined_at_class')->nullable();
            $table->unsignedTinyInteger('sibling_order_in_family')->nullable();
            $table->unsignedTinyInteger('status_in_family')->nullable();
            $table->unsignedTinyInteger('religion')->nullable();
            $table->boolean('is_active')->default(false);
            $table->longText('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }
};
