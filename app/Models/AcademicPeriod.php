<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasActiveState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property bool $is_active
 */
abstract class AcademicPeriod extends Model
{
    use HasActiveState;

    protected static function booted(): void
    {
        static::saved(function ($model) {
            if (! $model->wasChanged('is_active')) {
                return;
            }

            if ($model->is_active) {
                DB::transaction(function () {
                    Student::whereDoesntHave('enrollments', function (Builder $query) {
                        /** @var Builder<StudentEnrollment> $query */
                        // @phpstan-ignore-next-line
                        $query->active();
                    })->where('is_active', true)->update(['is_active' => false]);

                    Student::whereHas('enrollments', function (Builder $query) {
                        /** @var Builder<StudentEnrollment> $query */
                        // @phpstan-ignore-next-line
                        $query->active();
                    })->update(['is_active' => true]);
                });
            }

            cache()->deleteMultiple(['academic_period_ready', static::getActiveCacheKey()]);
        });
    }

    public static function getActive(): ?static
    {
        return cache()->remember(static::getActiveCacheKey(), now()->addDay(), fn () => static::query()->active()->first());
    }

    public static function getActiveCacheKey(): string
    {
        $name = str(class_basename(static::class))->snake();

        return "active_{$name}_record";
    }
}
