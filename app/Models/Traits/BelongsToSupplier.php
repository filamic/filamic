<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToSupplier
{
    /**
     * @return BelongsTo<Supplier, $this>
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
