<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Mail\WorkspaceInvitation;
use App\Models\Invite;
use App\Models\User;
use App\Models\Workspace;
use App\Services\CurrentWorkspace;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InviteController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Workspace $workspace)
    {
        $this->authorize('invite', $workspace);

        $validated = $request->validate([
            'email' => 'required|email',
            'role' => ['required', Rule::enum(Role::class)->except(Role::owner)],
        ]);

        if ($workspace->users()->where('email', $validated['email'])->exists()) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'User already a member of this workspace.',
            ]);
        }

        $existingInvite = Invite::where('workspace_id', $workspace->id)
            ->where('email', $validated['email'])
            ->first();

        if ($existingInvite && $existingInvite->isPending()) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'A pending invitation exists already.',
            ]);
        }

        Invite::where('workspace_id', $workspace->id)
            ->where('email', $validated['email'])
            ->delete();

        $invite = Invite::create([
            'workspace_id' => $workspace->id,
            'email' => $validated['email'],
            'role' => $validated['role'],
            'token' => Str::random(40),
            'invited_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($invite->email)->send(new WorkspaceInvitation($invite));

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Invitation sent successfully.',
        ]);
    }

    public function accept(string $token)
    {
        $invite = Invite::where('token', $token)->first();

        if (! $invite) {
            return redirect()
                ->route(auth()->check() ? 'dashboard' : 'login')
                ->with('toast', [
                    'type' => 'error',
                    'message' => 'The invitation link is invalid.',
                ]);
        }

        if (! $invite->isPending()) {
            return redirect()
                ->route(auth()->check() ? 'dashboard' : 'login')
                ->with('toast', [
                    'type' => 'error',
                    'message' => 'The invite has expired or was already used.',
                ]);
        }

        if (! auth()->check()) {
            session(['pending_invite_token' => $token]);

            $userExists = User::where('email', $invite->email)->exists();

            return $userExists
                ? redirect()->route('login')->with('toast', [
                    'type' => 'success',
                    'message' => "Log in as {$invite->email} to accept your invite.",
                ])
                : redirect()->route('register')->with('toast', [
                    'type' => 'success',
                    'message' => "Create an account with {$invite->email} to accept your invite.",
                ]);
        }

        if (auth()->user()->email !== $invite->email) {
            return redirect()->route('dashboard')->with('toast', [
                'type' => 'error',
                'message' => "This invite was sent to {$invite->email}. Log out and try again with that account.",
            ]);
        }

        $this->finalize($invite, auth()->user());

        return redirect()->route('dashboard')->with('toast', [
            'type' => 'success',
            'message' => "You've joined {$invite->workspace->name}.",
        ]);
    }

    protected function finalize(Invite $invite, User $user): void
    {
        $invite->workspace->users()->attach($user->id, ['role' => $invite->role->value]);
        $invite->update(['accepted_at' => now()]);
        app(CurrentWorkspace::class)->set($invite->workspace);
    }
}
