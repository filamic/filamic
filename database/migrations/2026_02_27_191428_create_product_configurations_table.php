<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_configurations', function (Blueprint $table) {
            $table->foreignUlid('product_item_id')->constrained();
            $table->foreignUlid('product_variation_option_id')->constrained();

            $table->primary(['product_item_id', 'product_variation_option_id']);
        });
    }
};
