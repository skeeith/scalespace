<script setup>
    defineProps({
        pluralName: {
            type: String,
            required: true
        }
    });
</script>

<template>
    <div class="hidden overflow-x-auto lg:block">
        <table class="w-full min-w-[1900px] text-left">
            <caption class="sr-only">List of {{ pluralName }}</caption>
            <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950 dark:group-hover:bg-slate-900/70">
                <slot name="thead" />
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <slot name="tbody" />
            </tbody>
        </table>
    </div>
    <div class="divide-y divide-slate-200 dark:divide-slate-800 lg:hidden">
        <slot name="mobile" />
    </div>
</template>
