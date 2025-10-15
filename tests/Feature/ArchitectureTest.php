<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;

arch('debug methods are not used', function () {
    expect(['dd', 'dump', 'var_dump', 'ray'])
        ->toOnlyBeUsedIn(AppServiceProvider::class);
});

arch('strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('ensure all tests are suffixed with Test')
    ->expect(fn () => collect(Illuminate\Support\Facades\File::allFiles('tests'))
        ->map->getPathName()
        ->filter(fn (string $path) => str($path)->startsWith(['tests/Feature', 'tests/Unit']))
        ->reject(fn (string $path) => str($path)->endsWith('Test.php'))
    )
    ->toBeEmpty();
