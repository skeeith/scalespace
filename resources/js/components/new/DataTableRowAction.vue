<script setup>
defineProps({ user: { type: Object, required: true } })
</script>

<template>
    <td class="sticky right-0 z-20 bg-white px-5 py-2 shadow-[-6px_0_10px_-8px_rgba(15,23,42,0.35)] transition-colors duration-150 group-hover:bg-slate-50 dark:bg-slate-900 dark:group-hover:bg-slate-950 dark:shadow-[-6px_0_10px_-8px_rgba(0,0,0,0.7)]">
        <div class="flex justify-end gap-2">
            <button type="button" class="inline-flex min-h-10 items-center gap-1.5 rounded-lg border border-slate-200 px-3 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 dark:border-slate-700 dark:text-slate-300 dark:hover:border-blue-800 dark:hover:bg-blue-950/40 dark:hover:text-blue-300 touch-manipulation"><svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" /><circle cx="12" cy="12" r="2.5" /></svg>View</button>
            <button type="button" class="inline-flex min-h-10 items-center gap-1.5 rounded-lg bg-blue-600 px-3 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20 touch-manipulation"><svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4L16.5 3.5Z" stroke-linecap="round" stroke-linejoin="round" /></svg>Edit</button>
        </div>
    </td>
</template>
