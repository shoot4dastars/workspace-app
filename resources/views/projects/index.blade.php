<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">{{ $workspace->name }} — Projects</h2>
                @can('create', [App\Models\Project::class, $workspace])
                    <x-primary-button :href="route('workspace.projects.create', $workspace)">
                        + New Project
                    </x-primary-button>
                @endcan
            </div>

            @if ($projects->isEmpty())
                <p class="text-gray-500">No projects yet. Create one to get started.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($projects as $project)
                        <a href="{{ route('workspace.projects.show', [$workspace, $project]) }}"
                           class="block bg-white shadow-sm rounded-lg p-5 hover:shadow-md transition">
                            <h3 class="font-semibold text-gray-900">{{ $project->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $project->description }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
