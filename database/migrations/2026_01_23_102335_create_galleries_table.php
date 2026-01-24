<?php

declare(strict_types=1);

use App\Models\GalleryCategory;
use App\Models\School;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(School::class)->nullable();
            $table->foreignIdFor(GalleryCategory::class)->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('images');
            $table->date('event_date');
            $table->timestamps();
        });
    }
};
