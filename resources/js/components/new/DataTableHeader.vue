<script setup>
const props = defineProps({
    column: { type: Object, required: true },
    sortKey: { type: String, required: true },
    sortDirection: { type: String, required: true }
})
const emit = defineEmits(['sort'])
const activateSort = () => emit('sort', props.column.key)
</script>

<template>
    <th scope="col" :class="column.key === 'name' ? 'sticky left-0 z-30 bg-slate-50 shadow-[6px_0_10px_-8px_rgba(15,23,42,0.35)] dark:bg-slate-950 dark:group-hover:bg-slate-900 dark:shadow-[6px_0_10px_-8px_rgba(0,0,0,0.7)]' : ''" class="whitespace-nowrap px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
        <button type="button" @click="activateSort" :aria-label="`Sort ${column.label} ${sortKey === column.key && sortDirection === 'asc' ? 'descending' : 'ascending'}`" class="group inline-flex min-h-10 items-center gap-2 rounded-lg text-left transition focus:outline-none focus:ring-4 focus:ring-blue-500/10 touch-manipulation">
            <span>{{ column.label }}</span>
            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md transition" :class="sortKey === column.key ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300' : 'text-slate-400 group-hover:text-blue-500 dark:text-slate-500 dark:group-hover:text-blue-400'">
                <svg v-if="sortKey !== column.key" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m8 9 4-4 4 4M16 15l-4 4-4-4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <svg v-else-if="sortDirection === 'asc'" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M12 18V6M7.5 10.5 12 6l4.5 4.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <svg v-else class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M12 6v12M7.5 13.5 12 18l4.5-4.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </span>
        </button>
    </th>
</template>
