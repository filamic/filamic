<?php

use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homeroom_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Teacher::class)->constrained();
            $table->foreignIdFor(Classroom::class)->constrained();
            $table->foreignIdFor(SchoolYear::class)->constrained();
            $table->timestamps();
        });
    }
};
