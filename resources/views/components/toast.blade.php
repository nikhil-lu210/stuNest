<div x-data="{
        show: false,
        message: '',
        type: 'success',
        showToast(event) {
            const raw = event.detail;
            const data = Array.isArray(raw) ? (raw[0] ?? {}) : (raw ?? {});
            this.message = data.message ?? '';
            this.type = data.type || 'success';
            this.show = true;
            setTimeout(() => { this.show = false; }, 3000);
        }
    }"
    @notify.window="showToast($event)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-8"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0 translate-x-8"
    style="display: none;"
    class="fixed top-5 right-5 z-[100] max-w-sm w-full bg-white rounded-xl shadow-2xl border border-gray-100 p-4 flex items-start gap-3 pointer-events-auto"
    role="status"
    aria-live="polite"
>
    <div class="flex-shrink-0 pt-0.5">
        <svg x-show="type === 'success'" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <svg x-show="type === 'error'" class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <svg x-show="type === 'warning'" class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
    </div>
    <div class="flex-1 min-w-0">
        <p x-text="message" class="text-sm font-medium text-gray-900"></p>
    </div>
    <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 shrink-0" aria-label="{{ __('Close') }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>
