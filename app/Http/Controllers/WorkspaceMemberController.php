<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use App\Models\Workspace;
use App\Services\CurrentWorkspace;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class WorkspaceMemberController extends Controller
{
    use AuthorizesRequests;
    public function leave(Workspace $workspace, CurrentWorkspace $currentWorkspace)
    {
        $member = $workspace->users()->where('user_id', auth()->id())->first();

        abort_if($member === null, 404);

        if ($member->pivot->role === Role::owner) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => "Transfer ownership before leaving.",
            ]);
        }

        $workspace->removeMember(auth()->user());

        if ($currentWorkspace->id() === $workspace->id) {
            $currentWorkspace->clear();
        }

        return redirect()->route('workspace.index')->with('toast', [
            'type' => 'success',
            'message' => "You've left {$workspace->name}",
        ]);
    }

    public function transferOwnership(Request $request, Workspace $workspace)
    {
        $this->authorize('transferOwnership', $workspace);

        $validated = $request->validate([
           'new_owner_id' => 'required|integer|exists:users,id',
        ]);

        $newOwner = User::findOrFail($validated['new_owner_id']);

        abort_unless($workspace->users()->where('user_id', $newOwner->id)->exists(), 422, 'The user is not a member of this workspace.');

        $workspace->transferOwnership($newOwner);

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Ownership transferred to {$newOwner->name}."
        ]);
    }

    public function remove(Workspace $workspace, User $member)
    {
        $this->authorize('manageMembers', $workspace);

        abort_if($member->id === auth()->id(), 422, 'Leave the workspace to remove yourself.');

        $targetPivot = $workspace->users()->where('user_id', $member->id)->first();

        abort_if($targetPivot === null, 404);

        abort_if($targetPivot->pivot->role === Role::owner, 422, 'Cannot remove the workspace owner. Transfer ownership first.');

        $workspace->removeMember($member);

        return back()->with('toast', [
            'type' => 'success',
            'message' => "{$member->name} was removed from the workspace.",
        ]);
    }
}
