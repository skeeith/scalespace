<script setup>
defineProps({
    currentPage: { type: Number, required: true },
    perPage: { type: Number, required: true },
    totalPages: { type: Number, required: true },
    pageNumbers: { type: Array, required: true },
    rangeStart: { type: Number, required: true },
    rangeEnd: { type: Number, required: true }
})
const emit = defineEmits(['update:per-page', 'go-to-page'])
const updatePerPage = (event) => emit('update:per-page', Number(event.target.value))
const goToPage = (page) => emit('go-to-page', page)
</script>

<template>
    <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <div class="flex items-center gap-2"><label for="users-per-page" class="text-xs font-medium text-slate-500 dark:text-slate-400">Items per page</label><select id="users-per-page" :value="perPage" @change="updatePerPage" class="h-10 rounded-lg border border-slate-200 bg-white px-2 text-sm font-semibold text-slate-700 outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 touch-manipulation"><option :value="5">5</option><option :value="8">8</option><option :value="12">12</option></select></div>
        <nav class="flex items-center justify-center gap-1" aria-label="Users pagination">
            <button type="button" @click="goToPage(1)" :disabled="currentPage === 1" class="hidden min-h-10 min-w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40 sm:flex dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 touch-manipulation" aria-label="First page">«</button>
            <button type="button" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" class="min-h-10 min-w-10 rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 touch-manipulation" aria-label="Previous page">‹</button>
            <button v-for="page in pageNumbers" :key="page" type="button" @click="goToPage(page)" :aria-current="page === currentPage ? 'page' : undefined" :class="page === currentPage ? 'bg-blue-600 text-white shadow-sm' : 'border border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800'" class="min-h-10 min-w-10 rounded-lg px-2 text-sm font-semibold transition touch-manipulation">{{ page }}</button>
            <button type="button" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages" class="min-h-10 min-w-10 rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 touch-manipulation" aria-label="Next page">›</button>
            <button type="button" @click="goToPage(totalPages)" :disabled="currentPage === totalPages" class="hidden min-h-10 min-w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40 sm:flex dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 touch-manipulation" aria-label="Last page">»</button>
        </nav>
    </div>
</template>
