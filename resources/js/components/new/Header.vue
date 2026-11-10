<script setup>
    defineProps({
        totalCount: {
            type: Number,
            required: true
        },
        pluralName: {
            type: String,
            required: true
        }
    });
</script>

<template>
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">Management</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">{{ pluralName }}</h1>
        </div>
        <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round" stroke-linejoin="round" /></svg>
            <div><p class="text-xs text-slate-500 dark:text-slate-400">Total {{ pluralName }}</p><p class="text-lg font-bold leading-5 text-slate-900 dark:text-white">{{ totalCount }}</p></div>
        </div>
    </section>
</template>
