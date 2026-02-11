<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('branch_id')->constrained();
            $table->string('name');
            $table->unsignedTinyInteger('level');
            $table->string('address')->nullable();
            $table->string('npsn')->nullable();
            $table->string('nis_nss_nds')->nullable();
            $table->string('telp')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('village')->nullable();
            $table->string('subdistrict')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'name']);
        });
    }
};
