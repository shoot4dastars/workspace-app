<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;

class CurrentWorkspace
{
    /**
     * Create a new class instance.
     */
    public function set(Workspace $workspace): void
    {
        session(['current_workspace_id' => $workspace->id]);
    }

    public function id(): ?int
    {
        return session('current_workspace_id');
    }

    public function get(User $user): ?Workspace
    {
        $workspaceId = $this->id();

        if (! $workspaceId) {
            return null;
        }

        return $user->workspaces->firstWhere('id', $workspaceId);
    }

    public function clear(): void
    {
        session()->forget('current_workspace_id');
    }
}
