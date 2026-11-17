<script setup>
    import { reactive, ref, watch, computed, provide, onBeforeMount, onMounted } from 'vue';
    import { onBeforeRouteUpdate, useRoute } from 'vue-router';
    import { index } from '@/composables/useIndex';

    import Header from '@/components/new/Header.vue';
    import FormTitle from '@/components/new/FormTitle.vue';
    import DataTable from '@/components/new/DataTable.vue';
    import DataTableHeader from '@/components/new/DataTableHeader.vue';
    import DataTableRow from '@/components/new/DataTableRow.vue';
    import Pagination from '@/components/new/Pagination.vue';

    const composableUseIndex = index({
        overrideRequiredPermissionsForView: [],
        additionalRequiredPermissionsForView: [],
        checkForPermissions: false
    });

    const {
        apiPath,
        routeName,
        routePrefixName,
        data,
        fieldNames,
        showProgress,
        userPermissions,
        getData,
        clearParameters,
        hasCompany,
        convertDate,
        convertYearOnly,
        convertDateNoYear,
        convertDateTime
    } = composableUseIndex;

    apiPath.value = '/api/users';
    routeName.value = 'users.index';
    routePrefixName.value = 'users';

    const route = useRoute();

    const action        = ref('View');
    const singularName  = ref('User');
    const pluralName    = ref('Users');
    const title         = ref('Users');
    const dataAttribute = ref('users');

    /**
     * Column names are field from the table/model, BUT they have additional data for display
     */
    const columnNames = [
        // { key: 'name_from_userable_from', label: 'From', value: null },
        { key: 'name', label: 'Name', value: null, is_sticky: true },
        { key: 'email', label: 'Email', value: null },
        { key: 'mobile_number', label: 'Mobile Number', value: null },
    ];
    
    /**
     * Field names are fields from the table, extract them from the columnNames variable, to make it a key value pair
     */
    fieldNames.value = reactive(Object.fromEntries(columnNames.map(({ key, value }) => [key, value])));

    // Provide for all child components
    provide('routeName', routeName);
    provide('routePrefixName', routePrefixName);
    provide('singularName', singularName);
    provide('pluralName', pluralName);
    provide('title', title);
    provide('dataAttribute', dataAttribute);
    provide('columnNames', columnNames);
    provide('fieldNames', fieldNames);
    provide('data', data);

    const checkForCustomPermissions = ref(false);
    const showAssignRolesForUsersPermission = ref(false);

    const requiredPermissionsForAssignRolesForUsers = [
        'full-access-and-permissions',
        'assign-roles-users'
    ];

    const fetchData = (query, perPage) => {
        getData({
            page: query.page,
            per_page: perPage,
            order_by: query.order_by,
            order_by_field_name: query.order_by_field_name,
            only_trashed: query.only_trashed,
            with_trashed: query.with_trashed,
            is_strict: query.is_strict,
            ...query
        }, dataAttribute.value);
    };

    onMounted(() => {
        fetchData(
            route.query,
            route.query.per_page ?? 15
        );
    });

    onBeforeRouteUpdate((to, from) => {
        fetchData(
            to.query,
            data.value?.meta?.per_page ?? 15
        );
    });

    watch(data, (newData) => {
        if (newData[dataAttribute.value].length == 0) {
            return;
        }

        const authenticatedUser = JSON.parse(localStorage.getItem('user') || 'null');

        if (Number(authenticatedUser?.id) !== 1) {
            newData[dataAttribute.value] = newData[dataAttribute.value].filter(user => user.name !== 'Super Administrator');
        }

        // const userableFrom = {
        //     'App\\Models\\Branch': 'Branch',
        //     'App\\Models\\Warehouse': 'Warehouse'
        // };

        // newData[dataAttribute.value].forEach(user => {
        //     if (
        //         typeof user.userable_from_type === 'string' &&
        //         typeof user.userable_from_id === 'number' &&
        //         userableFrom[user.userable_from_type]
        //     ) {
        //         user.userable_from_type = userableFrom[user.userable_from_type];
        //     }
        // });
    });

    const hasData = computed(() => 
        data.value && data?.[dataAttribute]?.length > 0
    );

    onBeforeMount(() => {
        if (checkForCustomPermissions.value) {
            const requiredPermissions = (Array.isArray(requiredPermissionsForAssignRolesForUsers)) ? requiredPermissionsForAssignRolesForUsers : [requiredPermissionsForAssignRolesForUsers];

            const hasPermission = requiredPermissions.some(permission => userPermissions.value.includes(permission));

            if (! hasPermission) {
                showAssignRolesForUsersPermission.value = false;
            }
        }
    });
</script>

<template>
    <main class="w-full px-4 py-5 sm:px-6 sm:py-6 lg:px-7 lg:py-7">
        <div class="mx-auto w-full max-w-[1800px] space-y-5 lg:space-y-6">
            <Header />

            <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <FormTitle />

                <DataTable>
                    <template #thead>
                        <tr>
                            <DataTableHeader />
                        </tr>
                    </template>

                    <template #tbody>
                        <DataTableRow />
                    </template>

                    <!-- <template #mobile>
                        <article v-for="user in visibleUsers" :key="user.id" class="p-4 sm:p-5" :class="user.deleted ? 'opacity-60' : ''">
                            <div class="flex items-start gap-3"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-cyan-500 text-xs font-bold text-white">{{ initials(user.name) }}</span><div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-2"><h3 class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ user.name }}</h3><span v-if="user.deleted" class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-bold uppercase text-red-600 dark:bg-red-950/40 dark:text-red-400">Trashed</span></div><p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ user.email }}</p></div></div>
                            <dl class="mt-4 grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-3 sm:grid-cols-2 dark:bg-slate-950 dark:group-hover:bg-slate-900/70">
                                <div><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Phone Number</dt><dd class="mt-1 break-words text-sm font-medium text-slate-700 dark:text-slate-200">{{ user.phone }}</dd></div>
                                <div><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Telephone Number</dt><dd class="mt-1 break-words text-sm font-medium text-slate-700 dark:text-slate-200">{{ user.telephone }}</dd></div>
                                <div><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Age</dt><dd class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-200">{{ user.age }}</dd></div>
                                <div><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Gender</dt><dd class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-200">{{ user.gender }}</dd></div>
                                <div><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Birthdate</dt><dd class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-200">{{ user.birthdate }}</dd></div>
                                <div><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Branch</dt><dd class="mt-1 break-words text-sm font-medium text-slate-700 dark:text-slate-200">{{ user.branch }}</dd></div>
                                <div><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">City</dt><dd class="mt-1 break-words text-sm font-medium text-slate-700 dark:text-slate-200">{{ user.city }}</dd></div>
                                <div><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Province</dt><dd class="mt-1 break-words text-sm font-medium text-slate-700 dark:text-slate-200">{{ user.province }}</dd></div>
                                <div class="sm:col-span-2"><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Roles</dt><dd class="mt-1 flex flex-wrap gap-1.5"><span v-for="role in user.roles" :key="role" class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">{{ role }}</span></dd></div>
                            </dl>
                            <div class="mt-3 grid grid-cols-2 gap-2"><button type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 touch-manipulation"><svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" /><circle cx="12" cy="12" r="2.5" /></svg>View</button><button type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-blue-600 text-sm font-semibold text-white transition hover:bg-blue-700 touch-manipulation"><svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4L16.5 3.5Z" stroke-linecap="round" stroke-linejoin="round" /></svg>Edit</button></div>
                        </article>

                        <div v-if="! visibleUsers.length" class="px-4 py-14 text-center"><p class="text-sm font-semibold text-slate-700 dark:text-slate-200">No users found</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Try changing your search or trash filter.</p></div>
                    </template> -->
                </DataTable>

                <!-- <Pagination :current-page="currentPage" :per-page="perPage" :total-pages="totalPages" :page-numbers="pageNumbers" :range-start="rangeStart" :range-end="rangeEnd" @update:per-page="perPage = $event; resetPage()" @go-to-page="goToPage" /> -->
            </section>
        </div>
    </main>
</template>
