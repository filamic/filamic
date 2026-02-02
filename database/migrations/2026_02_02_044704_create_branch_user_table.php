<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_user', function (Blueprint $table) {
            $table->foreignUlid('branch_id')->constrained();
            $table->foreignUlid('user_id')->constrained();
            $table->primary(['branch_id', 'user_id']);
        });
    }
};
