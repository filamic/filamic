<?php

declare(strict_types=1);

pest()->extend(Tests\TestCase::class)
 // ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

expect()->extend('toBeOne', fn () => $this->toBe(1));

function something()
{
    // ..
}
