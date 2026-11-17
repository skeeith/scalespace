<script setup>
    import { inject } from 'vue';
    import DataTableRowAction from './DataTableRowAction.vue';
    
    const routeName       = inject('routeName');
    const routePrefixName = inject('routePrefixName');
    const columnNames     = inject('columnNames');
    const fieldNames      = inject('fieldNames');
    const data            = inject('data');
    const dataAttribute   = inject('dataAttribute');
</script>

<template>
    <tr v-for="object in data?.[dataAttribute]" :key="object.id" class="group transition-colors duration-150 hover:bg-slate-50 dark:hover:bg-slate-950/60" :class="object.deleted != null ? 'opacity-60' : ''">
        <td v-for="column in columnNames" :key="column.key" class="px-5 py-2 text-sm" :class="column.is_sticky ? 'sticky left-0 z-20 bg-white shadow-[6px_0_10px_-8px_rgba(15,23,42,0.35)] transition-colors duration-150 group-hover:bg-slate-50 dark:bg-slate-900 dark:group-hover:bg-slate-950 dark:shadow-[6px_0_10px_-8px_rgba(0,0,0,0.7)]' : 'whitespace-nowrap text-slate-600 dark:text-slate-300'">
            <template v-if="column.is_sticky">
                <div class="flex items-center gap-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ object[column.key] }}</p>
                        <span v-if="object.deleted != null" class="text-[11px] font-semibold text-red-600 dark:text-red-400">Trashed</span>
                    </div>
                </div>
            </template>
            <template v-else>{{ object[column.key] }}</template>
        </td>
        <DataTableRowAction :object="object" />
    </tr>

    <!-- <td class="px-5 py-2"><div class="flex max-w-xs flex-wrap gap-1.5"><span v-for="role in user.roles" :key="role" class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">{{ role }}</span></div></td> -->
    <!-- <DataTableRowAction :user="user" /> -->

    <tr v-if="data?.[dataAttribute]?.length == 0">
        <td :colspan="columnNames.length + 1" class="px-5 py-14 text-center">
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">No {{ dataAttribute }} found</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Try changing your search or trash filter.</p>
        </td>
    </tr>
</template>
