<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Student;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToStudent
{
    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
