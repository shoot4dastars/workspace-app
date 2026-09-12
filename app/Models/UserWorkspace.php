<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserWorkspace extends Pivot
{
    protected $table = 'user_workspace';

    protected $fillable = ['role'];

    protected $casts = ['role' => Role::class];
}
