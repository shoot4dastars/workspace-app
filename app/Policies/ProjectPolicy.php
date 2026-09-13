<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $workspace->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        return $project->workspace->users()
            ->where('users.id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Workspace $workspace): bool
    {
        return $workspace->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return $project->workspace->users()
            ->where('users.id', $user->id)
            ->wherePivotIn('role', [Role::owner->value, Role::admin->value])
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $project->workspace->users()
            ->where('users.id', $user->id)
            ->wherePivotIn('role', [Role::owner->value, Role::admin->value])
            ->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return false;
    }
}
