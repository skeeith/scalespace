<script setup>
    const props = defineProps({
        search: {
            type: String,
            default: ''
        },
        includeTrash: {
            type: Boolean,
            default: false
        },
        trashOnly: {
            type: Boolean,
            default: false
        },
        rangeStart: {
            type: Number,
            default: 0
        },
        rangeEnd: {
            type: Number,
            default: 0
        },
        totalUsers: {
            type: Number,
            default: 0
        },
        sortDirection: {
            type: String,
            default: 'asc'
        },
        sortedByLabel: {
            type: String,
            default: 'Name'
        },
        sortedByDirectionLabel: {
            type: String,
            default: 'Ascending'
        }
    });

    const emit = defineEmits([
        'update:search',
        'reset-page',
        'toggle-trash-only',
        'toggle-include-trash',
        'clear-search',
        'show-active-only'
    ]);

    const updateSearch = (event) => {
        emit('update:search', event.target.value);
        emit('reset-page');
    };
</script>

<template>
    <div class="border-b border-slate-200 p-4 dark:border-slate-800 sm:p-5">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="relative min-w-0 flex-1 lg:max-w-xl">
                <span class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2" aria-hidden="true">
                    <svg class="absolute inset-0 h-5 w-5 text-slate-400 dark:text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7" />
                        <path d="m20 20-4-4" stroke-linecap="round" />
                    </svg>
                    <svg class="absolute left-[47%] top-[47%] h-4.5 w-4.5 -translate-x-1/2 -translate-y-1/2 drop-shadow-sm" viewBox="0 0 24 24" aria-hidden="true">
                        <defs>
                            <linearGradient id="searchLightningGradient" x1="4" y1="4" x2="19" y2="20" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#ef4444" />
                                <stop offset="1" stop-color="#facc15" />
                            </linearGradient>
                        </defs>
                        <path d="M13.1 2 7.2 13h4.3L10.6 22 16.8 11h-4.3L13.1 2Z" fill="url(#searchLightningGradient)" />
                    </svg>
                </span>
                <input :value="props.search" @input="updateSearch" type="search" placeholder="Search users by name, email, phone, location, branch, or role..." class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-slate-950 dark:group-hover:bg-slate-900 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-blue-500 dark:focus:bg-slate-950" aria-label="Search users" />
            </div>

            <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                <button type="button" @click="emit('toggle-trash-only')" :class="props.trashOnly ? 'border-red-500 bg-red-500 text-white dark:border-red-600 dark:bg-red-600 dark:text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-red-200 hover:bg-red-50 hover:text-red-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-red-900 dark:hover:bg-red-950/30 dark:hover:text-red-300'" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border px-3 text-sm font-semibold transition focus:outline-none focus:ring-4 focus:ring-blue-500/10 touch-manipulation" >
                    <span class="relative inline-flex h-5 w-5 shrink-0">
                        <svg :class="props.trashOnly ? 'absolute left-0 top-1 h-4.5 w-4.5 text-white dark:text-white' : 'absolute left-0 top-1 h-4.5 w-4.5 text-red-500 dark:text-red-400'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M8 7V5.5A1.5 1.5 0 0 1 9.5 4h5A1.5 1.5 0 0 1 16 5.5V7M5 7h14M7 7l.7 12.2a1.5 1.5 0 0 0 1.5 1.3h5.6a1.5 1.5 0 0 0 1.5-1.3L17 7" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <svg class="absolute left-2.5 top-0 h-3 w-3 text-slate-500 dark:text-slate-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M4 5h16l-6.5 7.5V18l-3 1.5v-7L4 5Z" />
                        </svg>
                    </span>
                    Trash Items Only
                </button>

                <button type="button" @click="emit('toggle-include-trash')" :class="props.includeTrash && !props.trashOnly ? 'border-red-200 bg-red-200 text-red-800 dark:border-red-800 dark:bg-red-900/40 dark:text-red-200' : 'border-slate-200 bg-white text-slate-700 hover:border-red-400 hover:bg-red-50 hover:text-red-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-red-700 dark:hover:bg-red-950/30 dark:hover:text-red-300'" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border px-3 text-sm font-semibold transition focus:outline-none focus:ring-4 focus:ring-blue-500/10 touch-manipulation" >
                    <span class="relative inline-flex h-5 w-5 shrink-0">
                        <svg :class="props.includeTrash && !props.trashOnly ? 'absolute left-0 top-1 h-4.5 w-4.5 text-red-700 dark:text-red-300' : 'absolute left-0 top-1 h-4.5 w-4.5 text-red-500 dark:text-red-400'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M8 7V5.5A1.5 1.5 0 0 1 9.5 4h5A1.5 1.5 0 0 1 16 5.5V7M5 7h14M7 7l.7 12.2a1.5 1.5 0 0 0 1.5 1.3h5.6a1.5 1.5 0 0 0 1.5-1.3L17 7" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <svg class="absolute left-2.5 top-0 h-3 w-3 text-slate-500 dark:text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                        </svg>
                    </span>
                    Include Trash Items
                </button>

                <button type="button" @click="emit('clear-search')" class="col-span-2 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-slate-600 dark:hover:bg-slate-800 sm:col-span-1 touch-manipulation" >
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M4 4v6h6M20 20v-6h-6M5.2 15a8 8 0 0 0 13.6 2.2L20 15M4 9l1.2-2.2A8 8 0 0 1 18.8 9" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Clear Search
                </button>
            </div>
        </div>

        <div class="mt-3 flex flex-col gap-3 text-xs text-slate-500 dark:text-slate-400 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <span>
                    Showing
                    <strong class="font-semibold text-slate-700 dark:text-slate-200">{{ props.rangeStart }}–{{ props.rangeEnd }}</strong>
                    of
                    <strong class="font-semibold text-slate-700 dark:text-slate-200">{{ props.totalUsers }}</strong>
                    users
                </span>
                <span class="hidden h-4 w-px bg-slate-200 dark:bg-slate-700 sm:block" aria-hidden="true"></span>
                <span class="inline-flex w-fit max-w-full items-center gap-1.5 rounded-full border border-blue-100 bg-blue-50/80 px-2.5 py-1.5 font-medium text-blue-700 dark:border-blue-900/60 dark:bg-blue-950/40 dark:text-blue-300">
                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path v-if="props.sortDirection === 'asc'" d="M12 18V6M7.5 10.5 12 6l4.5 4.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path v-else d="M12 6v12M7.5 13.5 12 18l4.5-4.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Sorted by</span>
                    <strong class="font-bold">{{ props.sortedByLabel }}</strong>
                    <span class="text-blue-400 dark:text-blue-500">·</span>
                    <strong class="font-bold">{{ props.sortedByDirectionLabel }}</strong>
                </span>
            </div>

            <button v-if="props.includeTrash || props.trashOnly" type="button" @click="emit('show-active-only')" class="w-fit font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 touch-manipulation" >
                Show active users only
            </button>
        </div>
    </div>
</template>
