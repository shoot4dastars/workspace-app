<?php

namespace App\Http\Middleware;

use App\Services\CurrentWorkspace;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentWorkspace
{
    public function __construct(protected CurrentWorkspace $currentWorkspace) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $workspace = $this->currentWorkspace->get($user);

            if (! $workspace) {
                $workspace = $user->workspaces()->first();

                if ($workspace) {
                    $this->currentWorkspace->set($workspace);
                }
            }

            View::share('currentWorkspace', $workspace);
        }

        return $next($request);
    }
}
