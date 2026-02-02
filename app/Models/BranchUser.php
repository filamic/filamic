<?php

namespace App\Models;

use App\Models\Traits\BelongsToUser;
use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BranchUser extends Pivot
{
    use BelongsToSchool;
    use BelongsToUser;
}
