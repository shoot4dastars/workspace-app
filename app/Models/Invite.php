<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invite extends Model
{
    protected $fillable = ['workspace_id', 'email', 'role', 'token', 'invited_by', 'expires_at', 'accepted_at'];

    protected $casts = [
        'role' => Role::class,
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->gt($this->expires_at);
    }

    public function isPending(): bool
    {
        return is_null($this->accepted_at) && !$this->isExpired();
    }
}
