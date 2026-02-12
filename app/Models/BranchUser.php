<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use App\Models\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property string $branch_id
 * @property string $user_id
 * @property-read Branch $branch
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BranchUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BranchUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BranchUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BranchUser whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BranchUser whereUserId($value)
 *
 * @mixin \Eloquent
 */
class BranchUser extends Pivot
{
    use BelongsToBranch;
    use BelongsToUser;
}
