<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\WorkspaceRequest;
use App\Models\Workspace;
use App\Services\CurrentWorkspace;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;

class WorkspaceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workspaces = auth()->user()->workspaces()->get();

        return view('workspace.index', compact('workspaces'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('workspace.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkspaceRequest $request)
    {
        $data = $request->validated();
        $userId = auth()->user()->id;

        $workspace = Workspace::create($data);

        $workspace->users()->attach($userId, ['role' => Role::owner->value]);

        return redirect()->route('workspace.index')->with('toast', [
            'type' => 'success',
            'message' => 'Your workspace was created successfully!',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Workspace $workspace)
    {
        abort_unless(auth()->user()->workspaces()->whereKey($workspace->id)->exists(), 403);

        $workspace->load('users');

        return view('workspace.show', compact('workspace'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workspace $workspace)
    {
        $this->authorize('update', $workspace);

        return view('workspace.edit', compact('workspace'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkspaceRequest $request, Workspace $workspace)
    {
        $this->authorize('update', $workspace);

        $data = $request->validated();

        $workspace->update($data);

        return redirect()->route('workspace.index')->with('toast', [
            'type' => 'success',
            'message' => 'Your workspace was updated successfully!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workspace $workspace, CurrentWorkspace $currentWorkspace)
    {
        $this->authorize('delete', $workspace);

        if ($currentWorkspace->id() === $workspace->id) {
            $currentWorkspace->clear();
        }

        $workspace->users()->detach();
        $workspace->delete();

        return redirect()->route('workspace.index')->with('toast', [
            'type' => 'success',
            'message' => 'Your workspace was deleted successfully!',
        ]);
    }

    public function switch(Workspace $workspace, CurrentWorkspace $currentWorkspace): RedirectResponse
    {
        abort_unless(auth()->user()->workspaces()->whereKey($workspace->id)->exists(), 403);

        $currentWorkspace->set($workspace);

        return back();
    }
}
