@if(session('toast'))
    <div
        x-data="{
            show: false,
            message: @js(session('toast.message')),
            type: @js(session('toast.type')),
            progress: 100
        }"
        x-init="
            $nextTick(() => show = true);
            let interval = setInterval(() => {
                progress -= 1;
                if (progress <= 0) {
                    clearInterval(interval);
                    show = false;
                }
            }, 40);
        "
        x-show="show"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed top-5 right-5 z-50 max-w-sm w-full bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden pointer-events-auto flex flex-col"
    >
        <div class="p-4 flex items-start gap-3">
            <!-- Text Content -->
            <div class="flex-1 pt-0.5">
                <p class="text-sm font-semibold text-gray-900" x-text="type === 'success' ? 'Success' : 'Error'"></p>
                <p class="mt-1 text-xs text-gray-500" x-text="message"></p>
            </div>

            <!-- Close Button -->
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Animated Progress Bar -->
        <div
            class="h-1 transition-all ease-linear"
            :class="type === 'success' ? 'bg-green-500' : 'bg-red-500'"
            :style="`width: ${progress}%`"
        ></div>
    </div>
@endif
