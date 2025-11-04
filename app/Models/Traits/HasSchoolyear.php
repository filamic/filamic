<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasSchoolyear
{
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->whereRelation('schoolYear', 'is_active', true);
    }

    public function canDelete(): bool
    {
        return $this->isActive();
    }

    public function isActive(): bool
    {
        return $this->schoolYear->isActive();
    }

    public function isInActive(): bool
    {
        return $this->schoolYear->isInactive();
    }
}
