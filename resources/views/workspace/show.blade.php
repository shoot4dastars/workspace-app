<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 sm:p-8">

                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">{{ $workspace->name }}</h2>
                        <p class="text-gray-500 mt-1">{{ $workspace->description }}</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('workspace.projects.index', $workspace) }}"
                           class="text-sm text-indigo-600 hover:underline">
                            View Projects
                        </a>

                        @can('update', $workspace)
                            <x-secondary-button :href="route('workspace.edit', $workspace)">Edit</x-secondary-button>
                        @endcan

                        @can('delete', $workspace)
                            <form method="POST" action="{{ route('workspace.destroy', $workspace) }}"
                                  onsubmit="return confirm('Delete this workspace? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <x-danger-button>Delete</x-danger-button>
                            </form>
                        @endcan
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-700 mb-3">Members</h3>
                <ul class="divide-y divide-gray-100">
                    @foreach ($workspace->users as $member)
                        <li class="py-2 flex justify-between items-center">
                            <span class="text-sm text-gray-800">{{ $member->name }}</span>

                            <div class="flex items-center gap-3">
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                                    {{ ucfirst($member->pivot->role->value) }}
                                </span>

                                @can('manageMembers', $workspace)
                                    @if ($member->pivot->role !== \App\Enums\Role::owner && $member->id !== auth()->id())
                                        <form method="POST" action="{{ route('workspace.members.remove', [$workspace, $member]) }}"
                                              onsubmit="return confirm('Remove {{ $member->name }} from this workspace?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:underline">Remove</button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </li>
                    @endforeach
                </ul>

                @can('transferOwnership', $workspace)
                    @if ($workspace->users->count() > 1)
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Transfer Ownership</h3>
                            <form method="POST" action="{{ route('workspace.transfer-ownership', $workspace) }}" class="flex gap-3">
                                @csrf
                                <select name="new_owner_id" class="border-gray-300 rounded-md shadow-sm flex-1" required>
                                    <option value="" disabled selected>Select a member</option>
                                    @foreach ($workspace->users->where('id', '!=', auth()->id()) as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }} ({{ ucfirst($member->pivot->role->value) }})</option>
                                    @endforeach
                                </select>
                                <x-secondary-button type="submit">Transfer</x-secondary-button>
                            </form>
                        </div>
                    @endif
                @endcan

                @can('invite', $workspace)
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Invite a Member</h3>
                        <form method="POST" action="{{ route('workspace.invites.store', $workspace) }}" class="flex gap-3 items-start">
                            @csrf
                            <div class="flex-1">
                                <x-text-input type="email" name="email" placeholder="email@example.com" class="w-full" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>
                            <select name="role" class="border-gray-300 rounded-md shadow-sm">
                                <option value="admin">Admin</option>
                                <option value="member" selected>Member</option>
                            </select>
                            <x-primary-button>Invite</x-primary-button>
                        </form>
                    </div>
                @endcan

                <div class="mt-8 pt-6 border-t border-gray-100">
                    @php
                        $myRole = $workspace->users->firstWhere('id', auth()->id())?->pivot->role;
                    @endphp

                    @if ($myRole === \App\Enums\Role::owner && $workspace->users->count() === 1)
                        {{-- sole owner: no leave option, must delete workspace instead --}}
                    @else
                        <form method="POST" action="{{ route('workspace.leave', $workspace) }}"
                              onsubmit="return confirm('Are you sure you want to leave this workspace?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:underline">Leave Workspace</button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
