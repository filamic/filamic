<?php

use App\Models\School;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_events', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(School::class)->nullable();
            $table->string('name');
            $table->string('location');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('image')->nullable();
            $table->longText('details')->nullable();
            $table->timestamps();
        });
    }
};
