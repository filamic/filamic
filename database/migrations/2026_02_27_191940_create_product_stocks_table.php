<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('product_item_id')->constrained();
            $table->foreignUlid('branch_id')->constrained();
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();

            $table->unique(['product_item_id', 'branch_id']);
        });
    }
};
