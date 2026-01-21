<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolEvent extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolEventFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at'   => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    #[Scope]
    protected function upcoming(Builder $query): Builder
    {
        return $query
            ->where('ends_at', '>=', now())
            ->orderBy('starts_at');
    }

    #[Scope]
    protected function ongoing(Builder $query): Builder
    {
        return $query
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    #[Scope]
    protected function past(Builder $query): Builder
    {
        return $query
            ->where('ends_at', '<', now())
            ->orderByDesc('ends_at');
    }
}
