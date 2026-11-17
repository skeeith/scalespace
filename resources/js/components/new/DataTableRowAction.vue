<script setup>
    import { inject, computed, onBeforeUnmount, onMounted, ref, useSlots } from 'vue';

    const props = defineProps({
        object: { type: Object, required: true, default: null },
        disableButtonSplitter: { type: Boolean, default: false },
        disableView: { type: Boolean, default: false },
        disableEdit: { type: Boolean, default: false },
        overrideRequiredPermissionsForView: { type: [Array, String], default: () => [] },
        overrideRequiredPermissionsForUpdate: { type: [Array, String], default: () => [] },
        checkForPermissions: { type: Boolean, default: false },
        isNested: { type: Boolean, default: false },
    })

    const routeName       = inject('routeName');
    const routePrefixName = inject('routePrefixName');
    const title           = inject('title');
    const columnNames     = inject('columnNames');
    const fieldNames      = inject('fieldNames');

    const slots = useSlots()
    const userPermissions = ref([])
    const showViewPermission = ref(true)
    const showUpdatePermission = ref(true)

    const rowObject = computed(() => props.object ?? props.user ?? {})

    const normalizePermissions = (permissions) => {
        if (Array.isArray(permissions)) {
            return permissions;
        }

        if (typeof permissions === 'string' && permissions.length > 0) {
            return [permissions];
        }

        return [];
    }

    const requiredPermissionsForView = computed(() => {
        const overrides = normalizePermissions(props.overrideRequiredPermissionsForView)

        return overrides.length > 0 ? overrides : ['full-access-and-permissions', `view-${routePrefixName.value}`];
    })

    const requiredPermissionsForUpdate = computed(() => {
        const overrides = normalizePermissions(props.overrideRequiredPermissionsForUpdate)

        return overrides.length > 0 ? overrides : ['full-access-and-permissions', `update-${routePrefixName.value}`];
    })

    const hasAnyPermission = (requiredPermissions) => {
        return requiredPermissions.some(permission => userPermissions.value.includes(permission))
    }

    const updatePermissionVisibility = () => {
        if (! props.checkForPermissions) {
            showViewPermission.value = true;
            showUpdatePermission.value = true;

            return;
        }

        showViewPermission.value = hasAnyPermission(requiredPermissionsForView.value)
        showUpdatePermission.value = hasAnyPermission(requiredPermissionsForUpdate.value)
    }

    const loadUserPermissions = () => {
        const storedPermissions = localStorage.getItem('permissions')

        try {
            const parsedPermissions = storedPermissions ? JSON.parse(storedPermissions) : []
            userPermissions.value = Array.isArray(parsedPermissions) ? parsedPermissions : []
        } catch {
            userPermissions.value = []
        }

        updatePermissionVisibility()
    }

    const routeToView = computed(() => {
        if (props.isNested) {
            return {
                name: `${routePrefixName.value}.view`,
                params: {
                    recordId: rowObject.value.record_id,
                    fileId: rowObject.value.id,
                },
            }
        }

        return {
            name: `${routePrefixName.value}.view`,
            params: { id: rowObject.value.id },
        }
    })

    const routeToEdit = computed(() => {
        if (props.isNested) {
            return {
                name: `${routePrefixName.value}.edit`,
                params: {
                    recordId: rowObject.value.record_id,
                    fileId: rowObject.value.id,
                },
            }
        }

        return {
            name: `${routePrefixName.value}.edit`,
            params: { id: rowObject.value.id },
        }
    })

    const hasDefaultSlot = computed(() => Boolean(slots.default));

    const handleUserRetrieved = () => {
        loadUserPermissions();
    }

    onMounted(() => {
        loadUserPermissions();

        Broadcast.$on('User-Retrieved', handleUserRetrieved);
    });

    onBeforeUnmount(() => {
        // Broadcast.$off('User-Retrieved', handleUserRetrieved);
    });
</script>

<template>
    <td class="sticky right-0 z-20 bg-white px-5 py-2 shadow-[-6px_0_10px_-8px_rgba(15,23,42,0.35)] transition-colors duration-150 group-hover:bg-slate-50 dark:bg-slate-900 dark:group-hover:bg-slate-950 dark:shadow-[-6px_0_10px_-8px_rgba(0,0,0,0.7)]">
        <div class="flex justify-end gap-2">
            <router-link v-if="! disableView && showViewPermission" :to="routeToView" class="inline-flex min-h-10 items-center gap-1.5 rounded-lg border border-slate-200 px-3 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 dark:border-slate-700 dark:text-slate-300 dark:hover:border-blue-800 dark:hover:bg-blue-950/40 dark:hover:text-blue-300 touch-manipulation">
                <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" />
                    <circle cx="12" cy="12" r="2.5" />
                </svg>
                View
            </router-link>

            <!-- <span v-if="! disableView && ! disableEdit && showViewPermission && showUpdatePermission" class="self-center text-slate-300 dark:text-slate-600" aria-hidden="true"> | </span> -->

            <router-link v-if="! disableEdit && showUpdatePermission" :to="routeToEdit" class="inline-flex min-h-10 items-center gap-1.5 rounded-lg bg-blue-600 px-3 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20 touch-manipulation">
                <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4L16.5 3.5Z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Edit
            </router-link>

            <span v-if="hasDefaultSlot && ! disableButtonSplitter" class="self-center text-slate-300 dark:text-slate-600" aria-hidden="true"> | </span>

            <slot name="default" />
        </div>
    </td>
</template>
