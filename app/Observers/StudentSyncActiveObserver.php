<?php

declare(strict_types=1);

namespace App\Observers;

class StudentSyncActiveObserver
{
    public function saved($model): void
    {
        defer(fn () => $model->student?->syncActiveStatus());
    }
}
