<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;

// arch()->preset()->php();

arch()->preset()->security();

// arch()->preset()->strict();

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

arch('migrations do not have down function')
    ->expect(fn () => collect(Illuminate\Support\Facades\File::allFiles('database/migrations'))
        ->map(fn ($file) => [
            'path' => $file->getPathName(),
            'content' => file_get_contents($file->getPathName()),
        ])
        ->filter(fn (array $file) => preg_match('/public\s+function\s+down\s*\(/', $file['content']))
        ->pluck('path')
    )
    ->toBeEmpty();
