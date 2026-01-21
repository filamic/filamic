<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SchoolEventController;
use App\Http\Controllers\Api\V1\FeaturedSchoolEventController;
use App\Http\Controllers\Api\V1\UpcomingSchoolEventController;

// Route::prefix('v1')->group(function () {
//     Route::get('upcoming-school-event',[UpcomingSchoolEventController::class,'index']);
// });

Route::prefix('v1')->group(function () {
    Route::get('school-events', [SchoolEventController::class, 'index']);
});