<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasUlids;
    
    /** @use HasFactory<\Database\Factories\BranchFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
