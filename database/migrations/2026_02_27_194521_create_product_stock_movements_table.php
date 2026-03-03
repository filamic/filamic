<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_stock_movements', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained();
            $table->foreignUlid('branch_id')->constrained();
            $table->foreignUlid('product_item_id')->constrained();
            $table->foreignUlid('related_movement_id')->nullable()->constrained('product_stock_movements');
            $table->foreignUlid('student_id')->nullable()->constrained();
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->dateTime('transaction_date')->nullable();
            $table->unsignedTinyInteger('type');
            $table->integer('quantity');
            $table->string('reference')->nullable();
            $table->string('notes')->nullable();

            $table->index(['product_item_id', 'branch_id', 'transaction_date'], 'idx_prod_item_branch_trans_date');
            $table->index(['branch_id', 'transaction_date'], 'idx_stock_movements_branch_trans_date');
            $table->timestamps();
        });
    }
};
