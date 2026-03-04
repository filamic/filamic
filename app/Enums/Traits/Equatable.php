<?php

declare(strict_types=1);

namespace App\Enums\Traits;

trait Equatable
{
    public function is(mixed ...$others): bool
    {
        return collect($others)
            ->filter(fn ($case) => $case instanceof self)
            ->some(fn (self $case) => $this === $case);
    }

    public function isNot(mixed ...$others): bool
    {
        return ! $this->is(...$others);
    }
}
