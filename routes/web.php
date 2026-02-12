<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Number;

Route::get('/', fn () => view('welcome'))->name('setup_warning');

Route::get('debug', function () {
    // echo(Number::currency((float) 500000, in: 'IDR', locale: config('app.locale'), precision:0));
    // 11 of 95 -> StudentForm ,Invoice, ListStudents
});
