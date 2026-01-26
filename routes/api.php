<?php

declare(strict_types=1);

use App\Http\Controllers\API\V1\GalleryController;
use App\Http\Controllers\Api\V1\SchoolEventController;
use App\Http\Controllers\Api\V1\UpcomingSchoolEventController;
use Illuminate\Support\Facades\Route;

// Route::prefix('v1')->group(function () {
//     Route::get('upcoming-school-event',[UpcomingSchoolEventController::class,'index']);
// });

Route::prefix('v1')->group(function () {
    Route::get('school-events', [SchoolEventController::class, 'index']);

    Route::get('galleries', [GalleryController::class, 'index']);
});
