<?php

declare(strict_types=1);

use App\Models\Position;

test('it prevents mass assignment to guarded id', function () {
    // ARRANGE
    $customId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

    // ACT
    $position = Position::create([
        'id' => $customId,
        'name' => 'Manager',
    ]);

    // ASSERT
    expect($position->getKey())
        ->toBeString()
        ->not->toBe($customId);
});
