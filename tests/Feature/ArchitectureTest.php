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

// arch('Filament resource tests have minimal required tests')
//     ->expect(function () {
//         $results = collect(Illuminate\Support\Facades\File::allFiles('tests/Feature/Filament/Admin/Resources'))
//             ->map(fn ($file) => [
//                 'path' => $file->getPathName(),
//                 'content' => file_get_contents($file->getPathName()),
//                 'name' => $file->getFilenameWithoutExtension(),
//             ])
//             ->filter(fn (array $file) => str_ends_with($file['name'], 'ResourceTest'))
//             ->map(function (array $file) {
//                 $content = $file['content'];
//                 $requiredTests = [
//                     'list page is accessible',
//                     'list page renders columns',
//                     'list page shows rows',
//                     'list page rows have view action',
//                     'create page is accessible',
//                     'cannot create a record without required fields',
//                     'can create a record',
//                     'view page is accessible',
//                     'view page shows all information',
//                     'view page has edit action',
//                     'edit page is accessible',
//                     'cannot save a record without required fields',
//                     'can save a record',
//                     'can save a record without changes',
//                 ];

//                 $missingTests = collect($requiredTests)
//                     ->filter(function (string $testName) use ($content) {
//                         // Check for exact match or variations
//                         $variations = [
//                             $testName,
//                             str_replace('shows all information', 'displays all information', $testName),
//                             str_replace('displays all information', 'shows all information', $testName),
//                         ];

//                         return ! collect($variations)->contains(fn (string $variation) => str_contains($content, "test('{$variation}'")
//                         );
//                     })
//                     ->values();

//                 return [
//                     'file' => $file['name'],
//                     'missing_tests' => $missingTests->toArray(),
//                     'has_all_required' => $missingTests->isEmpty(),
//                 ];
//             })
//             ->filter(fn (array $result) => ! $result['has_all_required']);

//         if ($results->isNotEmpty()) {
//             $results->each(function (array $result) {
//                 echo "❌ {$result['file']} is missing tests: " . implode(', ', $result['missing_tests']) . "\n";
//             });
//         }

//         return $results;
//     })
//     ->toBeEmpty();

// arch('Filament resource tests use correct imports and setup')
//     ->expect(fn () => collect(Illuminate\Support\Facades\File::allFiles('tests/Feature/Filament/Admin/Resources'))
//         ->map(fn ($file) => [
//             'path' => $file->getPathName(),
//             'content' => file_get_contents($file->getPathName()),
//             'name' => $file->getFilenameWithoutExtension(),
//         ])
//         ->filter(fn (array $file) => str_ends_with($file['name'], 'ResourceTest'))
//         ->map(function (array $file) {
//             $content = $file['content'];
//             $requiredImports = [
//                 'declare(strict_types=1);',
//                 'use Filament\\Actions\\Testing\\TestAction;',
//                 'use Livewire\\Livewire;',
//                 'beforeEach(fn () => $this->loginAdmin());',
//             ];

//             $missingImports = collect($requiredImports)
//                 ->filter(fn (string $import) => ! str_contains($content, $import))
//                 ->values();

//             return [
//                 'file' => $file['name'],
//                 'missing_imports' => $missingImports->toArray(),
//                 'has_all_imports' => $missingImports->isEmpty(),
//             ];
//         })
//         ->filter(fn (array $result) => ! $result['has_all_imports'])
//     )
//     ->toBeEmpty();
