<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Enums\ReligionEnum;
use App\Enums\StudentStatusEnum;
use App\Enums\StatusInFamilyEnum;
use App\Models\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;
    use HasUlids;
    use BelongsToUser;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'gender' => GenderEnum::class,
            'status_in_family' => StatusInFamilyEnum::class,
            'religion' => ReligionEnum::class,
            'status' => StudentStatusEnum::class,
            'metadata' => 'array'
        ];
    }

    public function father(): BelongsTo
    {
        return $this->belongsTo(User::class, 'father_id');
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mother_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', StudentStatusEnum::ACTIVE);
    }

    #[Scope]
    protected function graduated(Builder $query): Builder
    {
        return $query->where('status', StudentStatusEnum::GRADUATED);
    }

    #[Scope]
    protected function moved(Builder $query): Builder
    {
        return $query->where('status', StudentStatusEnum::MOVED);
    }

    #[Scope]
    protected function droppedOut(Builder $query): Builder
    {
        return $query->where('status', StudentStatusEnum::DROPPED_OUT);
    }

    #[Scope]
    protected function nonActive(Builder $query): Builder
    {
        return $query->where('status', StudentStatusEnum::NON_ACTIVE);
    }

    #[Scope]
    protected function prospective(Builder $query): Builder
    {
        return $query->where('status', StudentStatusEnum::PROSPECTIVE);
    }
}
