<script setup>
import DataTableRowAction from './DataTableRowAction.vue'
defineProps({
    user: { type: Object, required: true },
    initials: { type: Function, required: true }
})
</script>

<template>
    <tr class="group transition-colors duration-150 hover:bg-slate-50 dark:hover:bg-slate-950/60" :class="user.deleted ? 'opacity-60' : ''">
        <td class="sticky left-0 z-20 bg-white px-5 py-2 shadow-[6px_0_10px_-8px_rgba(15,23,42,0.35)] transition-colors duration-150 group-hover:bg-slate-50 dark:bg-slate-900 dark:group-hover:bg-slate-950 dark:shadow-[6px_0_10px_-8px_rgba(0,0,0,0.7)]"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-cyan-500 text-xs font-bold text-white">{{ initials(user.name) }}</span><div class="min-w-0"><p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ user.name }}</p><span v-if="user.deleted" class="text-[11px] font-semibold text-red-600 dark:text-red-400">Trashed</span></div></div></td>
        <td class="whitespace-nowrap px-5 py-2 text-sm text-slate-600 dark:text-slate-300">{{ user.email }}</td>
        <td class="whitespace-nowrap px-5 py-2 text-sm text-slate-600 dark:text-slate-300">{{ user.phone }}</td>
        <td class="whitespace-nowrap px-5 py-2 text-sm text-slate-600 dark:text-slate-300">{{ user.telephone }}</td>
        <td class="whitespace-nowrap px-5 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ user.age }}</td>
        <td class="whitespace-nowrap px-5 py-2 text-sm text-slate-600 dark:text-slate-300">{{ user.city }}</td>
        <td class="whitespace-nowrap px-5 py-2 text-sm text-slate-600 dark:text-slate-300">{{ user.province }}</td>
        <td class="whitespace-nowrap px-5 py-2 text-sm text-slate-600 dark:text-slate-300">{{ user.birthdate }}</td>
        <td class="whitespace-nowrap px-5 py-2 text-sm text-slate-600 dark:text-slate-300">{{ user.gender }}</td>
        <td class="whitespace-nowrap px-5 py-2 text-sm text-slate-600 dark:text-slate-300">{{ user.branch }}</td>
        <td class="px-5 py-2"><div class="flex max-w-xs flex-wrap gap-1.5"><span v-for="role in user.roles" :key="role" class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">{{ role }}</span></div></td>
        <DataTableRowAction :user="user" />
    </tr>
</template>
