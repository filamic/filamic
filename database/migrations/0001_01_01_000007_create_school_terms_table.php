<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_terms', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedTinyInteger('name')->unique();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }
};
