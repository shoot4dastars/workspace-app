<x-app-layout>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 sm:p-8">

                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                        {{ __('Create a Workspace') }}
                    </h2>
                </div>

                <form action="{{ route('workspace.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">

                        <div>
                            <x-input-label for="name" :value="__('Name')" />

                            <x-text-input
                                id="name"
                                class="block mt-2 w-full"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus
                            />

                            <x-input-error
                                :messages="$errors->get('name')"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />

                            <textarea
                                id="description"
                                name="description"
                                rows="5"
                                class="block mt-2 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >{{ old('description') }}</textarea>

                            <x-input-error
                                :messages="$errors->get('description')"
                                class="mt-2"
                            />
                        </div>

                        <div class="flex items-center justify-end pt-2">
                            <x-primary-button>
                                {{ __('Create Workspace') }}
                            </x-primary-button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

</x-app-layout>
