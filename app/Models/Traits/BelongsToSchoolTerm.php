<?php

namespace App\Models\Traits;

use App\Models\SchoolTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToSchoolTerm
{
    public function schoolTerm(): BelongsTo
    {
        return $this->belongsTo(SchoolTerm::class);
    }

    #[Scope]
    protected function activeTerm(Builder $query): Builder
    {
        return $query->whereRelation('schoolTerm', 'is_active', true);
    }
}
