<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">{{ $project->name }}</h2>
                    <p class="text-gray-500 mt-1">{{ $project->description }}</p>
                </div>

                <div class="flex items-center gap-3">
                    @can('update', $project)
                        <x-secondary-button :href="route('workspace.projects.edit', [$workspace, $project])">Edit</x-secondary-button>
                    @endcan
                    @can('delete', $project)
                        <form method="POST" action="{{ route('workspace.projects.destroy', [$workspace, $project]) }}"
                              onsubmit="return confirm('Delete this project and all its tasks?')">
                            @csrf @method('DELETE')
                            <x-danger-button>Delete</x-danger-button>
                        </form>
                    @endcan
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ openTask: null, openCreate: null }">
                @foreach ($columns as $status => $tasks)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                                {{ str_replace('_', ' ', $status) }}
                                <span class="text-gray-400 font-normal">({{ $tasks->count() }})</span>
                            </h3>

                            @can('create', [App\Models\Task::class, $project])
                                <button @click="openCreate = '{{ $status }}'" class="text-xs text-indigo-600 hover:underline">
                                    + Add Task
                                </button>
                            @endcan
                        </div>

                        <div class="space-y-3">
                            @forelse ($tasks as $task)
                                <div @click="openTask = {{ $task->id }}" class="bg-white rounded-md shadow-sm p-4 cursor-pointer hover:shadow-md transition">
                                    <p class="text-sm font-medium text-gray-900">{{ $task->title }}</p>

                                    @if ($task->priority)
                                        <span class="inline-block mt-2 text-xs px-2 py-0.5 rounded-full
                                            {{ match($task->priority->value) {
                                                'high' => 'bg-red-100 text-red-700',
                                                'medium' => 'bg-yellow-100 text-yellow-700',
                                                'low' => 'bg-green-100 text-green-700',
                                            } }}">
                                            {{ ucfirst($task->priority->value) }}
                                        </span>
                                    @endif

                                    <div class="mt-3 flex justify-between items-center text-xs text-gray-500">
                                        <span>{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                                        @if ($task->due_date)
                                            <span>{{ $task->due_date->format('M j') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic">No tasks</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach

                {{-- Create Task Modal (one shared modal, status set based on which column triggered it) --}}
                <template x-for="status in ['todo', 'in_progress', 'done']" :key="status">
                    <div x-show="openCreate === status" x-cloak
                         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
                         @click.self="openCreate = null">
                        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                            <h3 class="text-lg font-semibold mb-4">New Task</h3>

                            <form method="POST" :action="`{{ route('workspace.projects.tasks.store', [$workspace, $project]) }}`">
                                @csrf
                                <input type="hidden" name="status" :value="status">

                                <div class="space-y-4">
                                    <div>
                                        <x-input-label value="Title" />
                                        <x-text-input name="title" class="block mt-1 w-full" required />
                                    </div>

                                    <div>
                                        <x-input-label value="Description" />
                                        <textarea name="description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                                    </div>

                                    <div>
                                        <x-input-label value="Priority" />
                                        <select name="priority" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                            <option value="">None</option>
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>

                                    <div>
                                        <x-input-label value="Assignee" />
                                        <select name="assigned_to" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                            <option value="">Unassigned</option>
                                            @foreach ($workspace->users as $member)
                                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <x-input-label value="Due Date" />
                                        <x-text-input type="date" name="due_date" class="block mt-1 w-full" />
                                    </div>
                                </div>

                                <div class="flex justify-end gap-3 mt-6">
                                    <button type="button" @click="openCreate = null" class="text-sm text-gray-500">Cancel</button>
                                    <x-primary-button>Create Task</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                {{-- Edit Task Modals --}}
                @foreach ($columns as $status => $tasks)
                    @foreach ($tasks as $task)
                        <div x-show="openTask === {{ $task->id }}" x-cloak
                             class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
                             @click.self="openTask = null">
                            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                                <h3 class="text-lg font-semibold mb-4">Edit Task</h3>

                                <form method="POST" action="{{ route('workspace.projects.tasks.update', [$workspace, $project, $task]) }}">
                                    @csrf @method('PUT')

                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label value="Title" />
                                            <x-text-input name="title" class="block mt-1 w-full" value="{{ $task->title }}" required />
                                        </div>

                                        <div>
                                            <x-input-label value="Description" />
                                            <textarea name="description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ $task->description }}</textarea>
                                        </div>

                                        <div>
                                            <x-input-label value="Status" />
                                            <select name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                                @foreach (['todo', 'in_progress', 'done'] as $s)
                                                    <option value="{{ $s }}" {{ $task->status->value === $s ? 'selected' : '' }}>
                                                        {{ str_replace('_', ' ', ucfirst($s)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <x-input-label value="Priority" />
                                            <select name="priority" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                                <option value="">None</option>
                                                @foreach (['low', 'medium', 'high'] as $p)
                                                    <option value="{{ $p }}" {{ $task->priority?->value === $p ? 'selected' : '' }}>
                                                        {{ ucfirst($p) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <x-input-label value="Assignee" />
                                            <select name="assigned_to" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                                <option value="">Unassigned</option>
                                                @foreach ($workspace->users as $member)
                                                    <option value="{{ $member->id }}" {{ $task->assigned_to === $member->id ? 'selected' : '' }}>
                                                        {{ $member->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <x-input-label value="Due Date" />
                                            <x-text-input type="date" name="due_date" class="block mt-1 w-full"
                                                          value="{{ $task->due_date?->format('Y-m-d') }}" />
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center mt-6">
                                        @can('delete', $task)
                                            <button type="submit" form="delete-task-{{ $task->id }}" class="text-sm text-red-600 hover:underline">Delete</button>
                                        @endcan

                                        <div class="flex gap-3">
                                            <button type="button" @click="openTask = null" class="text-sm text-gray-500">Cancel</button>
                                            <x-primary-button>Save</x-primary-button>
                                        </div>
                                    </div>
                                </form>

                                @can('delete', $task)
                                    <form id="delete-task-{{ $task->id }}" method="POST"
                                          action="{{ route('workspace.projects.tasks.destroy', [$workspace, $project, $task]) }}"
                                          onsubmit="return confirm('Delete this task?')" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                @endforeach

            </div>

        </div>
    </div>
</x-app-layout>
