<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Workspace extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'role'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->using(UserWorkspace::class);
    }

    private static function generateUniqueSlug($name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count;
            $count++;
        }

        return $slug;
    }

    protected static function booted()
    {
        static::creating(function ($workspace) {
            $workspace->slug = static::generateUniqueSlug($workspace->name);
        });

        static::updating(function ($workspace) {
            if ($workspace->isDirty('name')) {
                $workspace->slug = static::generateUniqueSlug($workspace->name);
            }
        });
    }

    public function transferOwnership(User $newOwner): void
    {
        abort_unless($this->users()->wherePivot('user_id', $newOwner->id)->exists(), 422, 'New owner must be a member pf this workspace.');

        DB::transaction(function () use ($newOwner) {
            $this->users()
                ->wherePivot('role', Role::owner->value)
                ->update(['role' => Role::member->value]);

            $this->users()->updateExistingPivot($newOwner->id, ['role' => Role::owner->value]);
        });
    }

    public function removeMember(User $user): void
    {
        $pivot = $this->users()->wherePivot('user_id', $user->id)->first()?->pivot;

        abort_if($pivot === null, 404, 'User is not a member of this workspace.');

        abort_if(
            $pivot->role === Role::owner,
            422,
            'Owner must transfer ownership before leaving.'
        );

        $this->users()->detach($user->id);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
