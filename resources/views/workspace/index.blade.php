<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('My Workspaces') }}</h2>
                <a href="{{ route('workspace.create') }}" class="text-sm text-indigo-600 hover:underline">
                    + New Workspace
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($workspaces as $workspace)
                    <a href="{{ route('workspace.show', $workspace) }}"
                       class="flex flex-col bg-white shadow-sm rounded-lg p-5 hover:shadow-md transition">

                        <h3 class="font-semibold text-gray-900">{{ $workspace->name }}</h3>

                        <p class="text-sm text-gray-500 mt-1 min-h-[2.5rem] line-clamp-2">
                            {{ $workspace->description }}
                        </p>

                        <div class="mt-auto pt-3">
                            <span class="inline-block text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                                {{ ucfirst($workspace->pivot->role->value) }}
                            </span>
                        </div>

                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
