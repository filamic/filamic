<?php

namespace App\Models;

use App\Models\Traits\HasActiveState;
use Illuminate\Database\Eloquent\Model;

abstract class AcademicPeriod extends Model
{
    use HasActiveState;

    protected static function booted(): void
    {
        parent::booted();

        static::saved(function ($model) {
            if ($model->wasChanged('is_active')) {
                Student::query()->update(['is_active' => false]);
                cache()->forget(static::getActiveCacheKey());

                if($model->is_active === true){
                    Student::whereHas('enrollments', function ($query) {
                        $query->active();
                    })->update(['is_active' => true]);
                }
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
