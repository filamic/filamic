<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('supplier_id')->constrained();
            $table->foreignUlid('product_category_id')->constrained();
            $table->unsignedTinyInteger('level')->nullable();
            $table->unsignedTinyInteger('grade')->nullable();
            $table->string('name')->index();
            $table->string('description')->nullable();
            $table->string('fingerprint')->unique();
            $table->timestamps();
        });
    }
};
