<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('product_category_id')->constrained();
            $table->string('name');
            $table->timestamps();
        });
    }
};
