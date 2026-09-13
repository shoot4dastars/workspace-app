<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('project', function ($value, $route) {
            $workspaceParam = $route->parameter('workspace');

            $workspace = $workspaceParam instanceof Workspace
                ? $workspaceParam
                : Workspace::where('slug', $workspaceParam)->firstOrFail();

            return Project::where('slug', $value)
                ->where('workspace_id', $workspace->id)
                ->firstOrFail();
        });

        Route::bind('task', function ($value, $route) {
            $projectParam = $route->parameter('project');

            $project = $projectParam instanceof Project
                ? $projectParam
                : Project::where('slug', $projectParam)->firstOrFail();

            return Task::where('id', $value)->where('project_id', $project->id)->firstOrFail();
        });
    }
}
