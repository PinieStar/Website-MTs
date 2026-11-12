<div
    x-data="{
        toasts: [],
        add(event) {
            const id = Date.now() + Math.random();
            this.toasts.push({
                id,
                message: event.detail.message ?? '',
                type: event.detail.type ?? 'success',
            });
            setTimeout(() => this.remove(id), 3000);
        },
        remove(id) {
            this.toasts = this.toasts.filter((toast) => toast.id !== id);
        },
    }"
    @toast.window="add($event)"
    class="pointer-events-none fixed right-4 top-4 z-[60] flex w-full max-w-sm flex-col gap-2"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div class="pointer-events-auto flex items-start gap-3 rounded-lg px-4 py-3 text-sm font-medium text-white shadow-float"
             :class="toast.type === 'danger' ? 'bg-danger' : 'bg-success'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
            <p class="flex-1" x-text="toast.message"></p>
            <button type="button" @click="remove(toast.id)"
                    class="rounded p-0.5 text-white/80 hover:text-white" title="Tutup notifikasi">
                <x-admin.icon name="x" class="h-4 w-4" />
                <span class="sr-only">Tutup</span>
            </button>
        </div>
    </template>
</div>
