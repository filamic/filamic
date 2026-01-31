<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Classroom;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToClassroom
{
    /**
     * @return BelongsTo<Classroom, $this>
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}
