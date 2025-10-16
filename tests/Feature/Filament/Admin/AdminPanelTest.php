<?php

declare(strict_types=1);

test('authenticated users can access admin panel', function () {
    $this->loginAdmin();

    $this->get('/admin')
        ->assertOk();
});
