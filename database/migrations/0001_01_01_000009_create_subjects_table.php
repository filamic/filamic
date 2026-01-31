<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('subject_category_id')->constrained();
            $table->string('name');
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->timestamps();
        });
    }
};
