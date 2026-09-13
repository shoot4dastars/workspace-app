<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function store(TaskRequest $request, Workspace $workspace, Project $project)
    {
        $this->authorize('create', [Task::class, $project]);

        $task = $project->tasks()->create($request->validated());

        return redirect()->route('workspace.projects.show', [$workspace, $project])->with('toast', [
            'type' => 'success',
            'message' => 'Task created.',
        ]);
    }

    public function update(TaskRequest $request, Workspace $workspace, Project $project, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        return redirect()->route('workspace.projects.show', [$workspace, $project])->with('toast', [
            'type' => 'success',
            'message' => 'Task updated.',
        ]);
    }

    public function destroy(Workspace $workspace, Project $project, Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('workspace.projects.show', [$workspace, $project])->with('toast', [
            'type' => 'success',
            'message' => 'Task deleted.',
        ]);
    }
}
