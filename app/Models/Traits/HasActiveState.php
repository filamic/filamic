<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait HasActiveState
{
    protected function initializeHasActiveState(): void
    {
        $this->mergeCasts([
            'is_active' => 'boolean',
        ]);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function inactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function activateExclusively(): void
    {
        DB::transaction(function () {
            static::deactivateOthers();
            $this->update(['is_active' => true]);
        });
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isInactive(): bool
    {
        return ! $this->is_active;
    }

    public static function deactivateOthers(): void
    {
        static::query()
            ->lockForUpdate()
            ->active()
            ->update(['is_active' => false]);
    }

    public static function getActive(): ?static
    {
        $cacheKey = static::class . '_active_record';

        return cache()->rememberForever($cacheKey, fn () => static::query()->active()->first());
    }
}
