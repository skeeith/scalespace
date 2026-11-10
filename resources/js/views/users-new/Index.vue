<script setup>
    import { ref, watch, computed, onBeforeMount, onMounted } from 'vue';
    import { onBeforeRouteUpdate, useRoute } from 'vue-router';
    import { index } from '@/composables/useIndex';

    import Header from '@/components/new/Header.vue';
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
        getParameters,
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

    const checkForCustomPermissions = ref(false);
    const showAssignRolesForUsersPermission = ref(false);

    const requiredPermissionsForAssignRolesForUsers = [
        'full-access-and-permissions',
        'assign-roles-users'
    ];

    fieldNames.value = {
        name_from_userable_from: null,
        email: null,
        name: null
    };

    const fetchData = (query, perPage) => {
        getData({
            page: query.page,
            per_page: perPage,
            order_by: query.order_by,
            order_by_field_name: query.order_by_field_name,
            only_trashed: query.only_trashed,
            with_trashed: query.with_trashed,
            is_strict: query.is_strict,
            ...fieldNames.value
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
        if (! newData?.users) {
            return;
        }

        const authenticatedUser = JSON.parse(localStorage.getItem('user') || 'null');

        if (Number(authenticatedUser?.id) !== 1) {
            newData[dataAttribute.value] = newData[dataAttribute.value].filter(user => user.name !== 'Super Administrator');
        }

        const userableFrom = {
            'App\\Models\\Branch': 'Branch',
            'App\\Models\\Warehouse': 'Warehouse'
        };

        newData[dataAttribute.value].forEach(user => {
            if (
                typeof user.userable_from_type === 'string' &&
                typeof user.userable_from_id === 'number' &&
                userableFrom[user.userable_from_type]
            ) {
                user.userable_from_type = userableFrom[user.userable_from_type];
            }
        });
    });

    const hasData = computed(() => 
        data.value && Object.keys(data.value).length > 0
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
            <Header v-if="hasData" :pluralName="pluralName" :totalCount="data?.meta.total ?? 0" />

            <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 p-4 dark:border-slate-800 sm:p-5">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div class="relative min-w-0 flex-1 lg:max-w-xl">
                            <span class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2" aria-hidden="true"><svg class="absolute inset-0 h-5 w-5 text-slate-400 dark:text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" stroke-linecap="round" /></svg><svg class="absolute left-[47%] top-[47%] h-4.5 w-4.5 -translate-x-1/2 -translate-y-1/2 drop-shadow-sm" viewBox="0 0 24 24" aria-hidden="true"><defs><linearGradient id="searchLightningGradient" x1="4" y1="4" x2="19" y2="20" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#ef4444" /><stop offset="1" stop-color="#facc15" /></linearGradient></defs><path d="M13.1 2 7.2 13h4.3L10.6 22 16.8 11h-4.3L13.1 2Z" fill="url(#searchLightningGradient)" /></svg></span>
                            <input v-model="search" @input="resetPage" type="search" placeholder="Search users by name, email, phone, location, branch, or role..." class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-slate-950 dark:group-hover:bg-slate-900 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-blue-500 dark:focus:bg-slate-950" aria-label="Search users" />
                        </div>
                        <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                            <button type="button" @click="toggleTrashOnly" :class="trashOnly ? 'border-red-500 bg-red-500 text-white dark:border-red-600 dark:bg-red-600 dark:text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-red-200 hover:bg-red-50 hover:text-red-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-red-900 dark:hover:bg-red-950/30 dark:hover:text-red-300'" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border px-3 text-sm font-semibold transition focus:outline-none focus:ring-4 focus:ring-blue-500/10 touch-manipulation"><span class="relative inline-flex h-5 w-5 shrink-0"><svg :class="trashOnly ? 'absolute left-0 top-1 h-4.5 w-4.5 text-white dark:text-white' : 'absolute left-0 top-1 h-4.5 w-4.5 text-red-500 dark:text-red-400'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 7V5.5A1.5 1.5 0 0 1 9.5 4h5A1.5 1.5 0 0 1 16 5.5V7M5 7h14M7 7l.7 12.2a1.5 1.5 0 0 0 1.5 1.3h5.6a1.5 1.5 0 0 0 1.5-1.3L17 7" stroke-linecap="round" stroke-linejoin="round" /></svg><svg class="absolute left-2.5 top-0 h-3 w-3 text-slate-500 dark:text-slate-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 5h16l-6.5 7.5V18l-3 1.5v-7L4 5Z" /></svg></span>Trash Items Only</button>
                            <button type="button" @click="toggleIncludeTrash" :class="includeTrash && !trashOnly ? 'border-red-200 bg-red-200 text-red-800 dark:border-red-800 dark:bg-red-900/40 dark:text-red-200' : 'border-slate-200 bg-white text-slate-700 hover:border-red-400 hover:bg-red-50 hover:text-red-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-red-700 dark:hover:bg-red-950/30 dark:hover:text-red-300'" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border px-3 text-sm font-semibold transition focus:outline-none focus:ring-4 focus:ring-blue-500/10 touch-manipulation"><span class="relative inline-flex h-5 w-5 shrink-0"><svg :class="includeTrash && !trashOnly ? 'absolute left-0 top-1 h-4.5 w-4.5 text-red-700 dark:text-red-300' : 'absolute left-0 top-1 h-4.5 w-4.5 text-red-500 dark:text-red-400'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 7V5.5A1.5 1.5 0 0 1 9.5 4h5A1.5 1.5 0 0 1 16 5.5V7M5 7h14M7 7l.7 12.2a1.5 1.5 0 0 0 1.5 1.3h5.6a1.5 1.5 0 0 0 1.5-1.3L17 7" stroke-linecap="round" stroke-linejoin="round" /></svg><svg class="absolute left-2.5 top-0 h-3 w-3 text-slate-500 dark:text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-linecap="round" /></svg></span>Include Trash Items</button>
                            <button type="button" @click="clearSearch" class="col-span-2 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-slate-600 dark:hover:bg-slate-800 sm:col-span-1 touch-manipulation"><svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 4v6h6M20 20v-6h-6M5.2 15a8 8 0 0 0 13.6 2.2L20 15M4 9l1.2-2.2A8 8 0 0 1 18.8 9" stroke-linecap="round" stroke-linejoin="round" /></svg>Clear Search</button>
                        </div>
                    </div>
                    <div class="mt-3 flex flex-col gap-3 text-xs text-slate-500 dark:text-slate-400 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-2">
                            <span>Showing <strong class="font-semibold text-slate-700 dark:text-slate-200">{{ rangeStart }}–{{ rangeEnd }}</strong> of <strong class="font-semibold text-slate-700 dark:text-slate-200">{{ data[dataAttribute.value].length }}</strong> users</span>
                            <span class="hidden h-4 w-px bg-slate-200 dark:bg-slate-700 sm:block" aria-hidden="true"></span>
                            <span class="inline-flex w-fit max-w-full items-center gap-1.5 rounded-full border border-blue-100 bg-blue-50/80 px-2.5 py-1.5 font-medium text-blue-700 dark:border-blue-900/60 dark:bg-blue-950/40 dark:text-blue-300"><svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path v-if="sortDirection === 'asc'" d="M12 18V6M7.5 10.5 12 6l4.5 4.5" stroke-linecap="round" stroke-linejoin="round" /><path v-else d="M12 6v12M7.5 13.5 12 18l4.5-4.5" stroke-linecap="round" stroke-linejoin="round" /></svg><span>Sorted by</span><strong class="font-bold">{{ sortedByLabel }}</strong><span class="text-blue-400 dark:text-blue-500">·</span><strong class="font-bold">{{ sortedByDirectionLabel }}</strong></span>
                        </div>
                        <button v-if="includeTrash || trashOnly" type="button" @click="showActiveOnly" class="w-fit font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 touch-manipulation">Show active users only</button>
                    </div>
                </div>

                <DataTable :pluralName="pluralName">
                    <template #thead>
                        <tr>
                            <DataTableHeader v-for="column in columns" :key="column.key" :column="column" :sort-key="sortKey" :sort-direction="sortDirection" @sort="sortBy" />
                            <th scope="col" class="sticky right-0 z-30 whitespace-nowrap bg-slate-50 px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-600 shadow-[-6px_0_10px_-8px_rgba(15,23,42,0.35)] dark:bg-slate-950 dark:group-hover:bg-slate-900 dark:text-slate-300 dark:shadow-[-6px_0_10px_-8px_rgba(0,0,0,0.7)]">Options</th>
                        </tr>
                    </template>

                    <template #tbody>
                        <DataTableRow v-for="user in visibleUsers" :key="user.id" :user="user" :initials="initials" />
                        <tr v-if="!visibleUsers.length"><td colspan="12" class="px-5 py-14 text-center"><p class="text-sm font-semibold text-slate-700 dark:text-slate-200">No users found</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Try changing your search or trash filter.</p></td></tr>
                    </template>

                    <template #mobile>
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
                    </template>
                </DataTable>

                <Pagination :current-page="currentPage" :per-page="perPage" :total-pages="totalPages" :page-numbers="pageNumbers" :range-start="rangeStart" :range-end="rangeEnd" @update:per-page="perPage = $event; resetPage()" @go-to-page="goToPage" />

            </section>
        </div>
    </main>
</template>
