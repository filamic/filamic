<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_learning_group', function (Blueprint $table) {
            $table->foreignUlid('learning_group_id')->constrained();
            $table->foreignUlid('student_id')->constrained();
            $table->primary(['learning_group_id', 'student_id']);
        });
    }
};
