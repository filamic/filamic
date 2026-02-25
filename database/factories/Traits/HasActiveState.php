<?php

declare(strict_types=1);

namespace Database\Factories\Traits;

trait HasActiveState
{
    public function active(): static
    {
        return $this->state([
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state([
            'is_active' => false,
        ]);
    }
}
