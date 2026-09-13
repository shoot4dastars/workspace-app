<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index(Workspace $workspace)
    {
        $this->authorize('viewAny', [Project::class, $workspace]);

        $projects = $workspace->projects()->latest()->get();

        return view('projects.index', compact('projects', 'workspace'));
    }

    public function create(Workspace $workspace)
    {
        $this->authorize('create', [Project::class, $workspace]);

        return view('projects.create', compact('workspace'));
    }

    public function store(ProjectRequest $request, Workspace $workspace)
    {
        $this->authorize('create', [Project::class, $workspace]);

        $project = $workspace->projects()->create($request->validated());

        return redirect()->route('workspace.projects.show', [$workspace, $project])->with('toast', [
            'type' => 'success',
            'message' => 'Project created.',
        ]);
    }

    public function show(Workspace $workspace, Project $project)
    {
        $this->authorize('view', $project);

        $tasks = $project->tasks()->with('assignee')->get();

        $columns = [
            'todo' => $tasks->where('status', TaskStatus::todo),
            'in_progress' => $tasks->where('status', TaskStatus::in_progress),
            'done' => $tasks->where('status', TaskStatus::done),
        ];

        return view('projects.show', compact('workspace', 'project', 'columns'));
    }

    public function edit(Workspace $workspace, Project $project)
    {
        $this->authorize('update', $project);

        return view('projects.edit', compact('workspace', 'project'));
    }

    public function update(ProjectRequest $request, Workspace $workspace, Project $project)
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        return redirect()->route('workspace.projects.show', [$workspace, $project])->with('toast', [
            'type' => 'success',
            'message' => 'Project updated.',
        ]);
    }

    public function destroy(Workspace $workspace, Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('workspace.projects.index', $workspace)->with('toast', [
            'type' => 'success',
            'message' => 'Project deleted.',
        ]);
    }
}
