<script setup>
    import { reactive, ref, watch, inject, onMounted } from 'vue';
    import { useRouter, useRoute } from 'vue-router';

    const routeName = inject('routeName');
    const routePrefixName = inject('routePrefixName');
    const columnNames = inject('columnNames');
    const fieldNames = inject('fieldNames');

    const router = useRouter();
    const route = useRoute();

    // variables
    const isLoading = ref(false);

    // column/field variables
    let order_by_field_name = ref('id');
    let order_by = ref(null);

    onMounted(() => {
        order_by_field_name.value = route.query.order_by_field_name;
        order_by.value = route.query.order_by;

        checkSortingForDefault();
        checkSortingForAscending();
        checkSortingForDescending();
    });

    watch(
        () => route.query.order_by, (new_order_by, old_order_by) => {
            if (new_order_by === old_order_by) {
                return;
            }

            order_by.value = new_order_by;
            order_by_field_name.value = route.query.order_by_field_name ?? '';

            checkSortingForDefault(order_by_field_name.value);
            checkSortingForAscending(order_by_field_name.value);
            checkSortingForDescending(order_by_field_name.value);
        },
        () => route.query, () => {
            checkSortingForDefault(order_by_field_name.value);
            checkSortingForAscending(order_by_field_name.value);
            checkSortingForDescending(order_by_field_name.value);
        },
        { /*immediate: true,*/ deep: true }
    );

    const checkSortingForDefault = (column) => {
        if (
            route.query.order_by_field_name !== column &&
            (route.query.order_by !== 'desc' || route.query.order_by !== 'asc')
        ) {
            return true;
        }

        return false;
    }

    const checkSortingForAscending = (column) => {
        if (
            (order_by.value === 'asc' && route.query.order_by_field_name === column) ||
            (route.query.order_by === 'asc' && route.query.order_by_field_name === column)
        ) {
            return true;
        }

        return false;
    }

    const checkSortingForDescending = (column) => {
        if (
            (order_by.value === 'desc' && route.query.order_by_field_name === column) ||
            (route.query.order_by === 'desc' && route.query.order_by_field_name === column)
        ) {
            return true;
        }

        return false;
    }

    const sortByFieldName = async (columnName) => {
        if (route.query.order_by === 'asc' && route.query.order_by_field_name === columnName) {
            order_by_field_name.value = 'id';
            order_by.value = null;
        } else if (
            (typeof order_by.value === 'object' && typeof route.query.order_by_field_name === 'undefined' && typeof route.query.order_by === 'undefined') ||
            (typeof order_by.value === 'object' && typeof route.query.order_by_field_name === 'string' && typeof route.query.order_by === 'string') ||
            (typeof order_by.value === 'object' && typeof route.query.order_by_field_name === 'string' && typeof route.query.order_by === 'object') ||
            (
                typeof order_by.value === 'string' &&
                order_by.value === 'desc' &&
                typeof route.query.order_by === 'string' &&
                route.query.order_by === 'desc' &&
                typeof route.query.order_by_field_name === 'string' &&
                route.query.order_by_field_name !== columnName
            ) ||
            (
                typeof order_by.value === 'string' &&
                order_by.value === 'asc' &&
                typeof route.query.order_by === 'string' &&
                route.query.order_by === 'asc' &&
                typeof route.query.order_by_field_name === 'string' &&
                route.query.order_by_field_name !== columnName
            ) ||
            (
                typeof order_by.value === 'undefined' &&
                typeof route.query.order_by === 'undefined' &&
                typeof route.query.order_by_field_name === 'undefined' &&
                route.query.order_by_field_name !== columnName
            )
        ) {
            order_by_field_name.value = columnName;
            order_by.value = 'desc';
        } else if (
            (
                typeof order_by.value === 'string' &&
                order_by.value === 'desc' &&
                typeof route.query.order_by_field_name === 'string' &&
                route.query.order_by_field_name === columnName
            ) ||
            (
                typeof order_by.value === 'object' &&
                order_by.value === null &&
                typeof route.query.order_by === 'string' &&
                route.query.order_by === 'desc' &&
                typeof route.query.order_by_field_name === 'string' &&
                route.query.order_by_field_name === columnName
            )
        ) {
            order_by_field_name.value = columnName;
            order_by.value = 'asc';
        }

        isLoading.value = true;

        const navigationResult = await router.push({
            name: routeName.value,
            query: {
                page: route.query.page,
                per_page: route.query.per_page,
                order_by: order_by.value,
                order_by_field_name: order_by_field_name.value,
                with_trashed: route.query.with_trashed,
                only_trashed: route.query.only_trashed,
                ...fieldNames.value,
            }
        });

        if (navigationResult) {
            console.log('Navigation prevented or aborted');
        }

        isLoading.value = false;
    }
</script>

<template>
    <th v-for="column in columnNames" :column="column" :key="column.key" scope="col" :class="column.is_sticky ? 'sticky left-0 z-30 bg-slate-50 shadow-[6px_0_10px_-8px_rgba(15,23,42,0.35)] dark:bg-slate-950 dark:group-hover:bg-slate-900 dark:shadow-[6px_0_10px_-8px_rgba(0,0,0,0.7)]' : ''" class="whitespace-nowrap px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
        <button type="button" @click="sortByFieldName(column.key)" :aria-label="`Sort ${column.label} ${route.query.order_by_field_name === column.key && route.query.order_by === 'asc' ? 'descending' : 'ascending'}`" class="group inline-flex min-h-10 items-center gap-2 rounded-lg text-left transition focus:outline-none focus:ring-4 focus:ring-blue-500/10 touch-manipulation">
            <span>{{ column.label }}</span>
            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md transition" :class="route.query.order_by_field_name === column.key ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300' : 'text-slate-400 group-hover:text-blue-500 dark:text-slate-500 dark:group-hover:text-blue-400'">
                <svg v-if="checkSortingForDefault(column.key)" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m8 9 4-4 4 4M16 15l-4 4-4-4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <svg v-else-if="checkSortingForAscending(column.key)" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M12 18V6M7.5 10.5 12 6l4.5 4.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                <svg v-else-if="checkSortingForDescending(column.key)" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M12 6v12M7.5 13.5 12 18l4.5-4.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </span>
        </button>
    </th>
    <th scope="col" class="sticky right-0 z-30 whitespace-nowrap bg-slate-50 px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-600 shadow-[-6px_0_10px_-8px_rgba(15,23,42,0.35)] dark:bg-slate-950 dark:group-hover:bg-slate-900 dark:text-slate-300 dark:shadow-[-6px_0_10px_-8px_rgba(0,0,0,0.7)]">Options</th>
</template>
