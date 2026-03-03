<?php

declare(strict_types=1);

namespace App\Enums\Traits;

trait Equatable
{
    public function is(...$others): bool
    {
        return collect($others)
            ->filter(fn ($case) => $case instanceof self)
            ->some(fn (self $case) => $this === $case);
    }

    public function isNot(...$others): bool
    {
        return ! $this->is(...$others);
    }
}
