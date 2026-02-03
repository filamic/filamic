<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasActiveState;
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
        parent::booted();

        static::saved(function ($model) {
            if ($model->wasChanged('is_active')) {
                DB::transaction(function () use ($model) {
                    Student::query()->update(['is_active' => false]);

                    if ($model->is_active === true) {
                        Student::whereHas('enrollments', function ($query) {
                            /** @var StudentEnrollment $query */
                            // @phpstan-ignore-next-line
                            $query->active();
                        })->update(['is_active' => true]);
                    }
                });

                cache()->forget(static::getActiveCacheKey());
            }
        });
    }

    public static function getActive(): ?static
    {
        return cache()->rememberForever(static::getActiveCacheKey(), fn () => static::query()->active()->first());
    }

    public static function getActiveCacheKey(): string
    {
        $name = str(class_basename(static::class))->snake();

        return "active_{$name}_record";
    }
}
