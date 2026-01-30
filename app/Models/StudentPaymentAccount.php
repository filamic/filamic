<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use App\Models\Traits\BelongsToStudent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class StudentPaymentAccount extends Model
{
    use HasUlids;
    use BelongsToSchool;
    use BelongsToStudent;

    protected $guarded = ['id'];
}
