<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = ['workspace_id', 'name', 'slug', 'description'];

    private static function generateUniqueSlug($name, $workspaceId): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('workspace_id', $workspaceId)->exists()) {
            $slug = $originalSlug.'-'.$count;
            $count++;
        }

        return $slug;
    }

    protected static function booted()
    {
        static::creating(function ($project) {
            $project->slug = static::generateUniqueSlug($project->name, $project->workspace_id);
        });

        static::updating(function ($project) {
            if ($project->isDirty('name')) {
                $project->slug = static::generateUniqueSlug($project->name, $project->workspace_id);
            }
        });
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
