{{-- Toast stack + flash messages from the session. Push more with window.toast('success', '...'). --}}
<div
    x-data
    x-init="
        @if (session('success')) $store.toast.push('success', @js(session('success'))); @endif
        @if (session('error')) $store.toast.push('error', @js(session('error')), 6000); @endif
    "
    class="pointer-events-none fixed inset-x-4 bottom-4 z-[90] flex flex-col items-center gap-2 sm:inset-x-auto sm:right-4 sm:items-end"
    aria-live="polite"
>
    <template x-for="t in $store.toast.items" :key="t.id">
        <div
            x-transition.opacity
            class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-lg border border-line bg-surface p-4 shadow-pop sm:w-96"
            role="status"
        >
            <span
                class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
                :class="{
                    'bg-mint-50 text-mint-800 dark:bg-mint-400/15 dark:text-mint-300': t.type === 'success',
                    'bg-danger-50 text-danger-700 dark:bg-danger-500/15 dark:text-red-300': t.type === 'error',
                    'bg-brand-50 text-brand-700 dark:bg-brand-500/20 dark:text-brand-300': t.type === 'info',
                }"
            >
                <i class="fa-solid" :class="{ 'fa-check': t.type === 'success', 'fa-triangle-exclamation': t.type === 'error', 'fa-info': t.type === 'info' }" aria-hidden="true"></i>
            </span>
            <p class="flex-1 pt-1 text-small font-medium" x-text="t.message"></p>
            <button type="button" class="btn-icon -mr-2 -mt-1 h-8 w-8" @click="$store.toast.remove(t.id)" aria-label="Tutup notifikasi">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
    </template>
</div>
