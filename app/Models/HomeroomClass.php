<?php

namespace App\Models;

use App\Models\Traits\HasSchoolyear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomeroomClass extends Model
{
    /** @use HasFactory<\Database\Factories\HomeroomClassFactory> */
    use HasFactory, HasSchoolyear;

    protected $guarded = ['id'];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
