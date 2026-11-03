import { ref, onBeforeMount, onMounted, onBeforeUnmount } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import { DateTime } from 'luxon';

export function useIndexSetup(props) {
    const router = useRouter();
    const route = useRoute();

    // Reactive data
    const userPermissions = ref([]);
    const requiredPermissionsForView = ref([]);
    const redirectTo = ref(null);

    const routePrefixName = ref('');
    const showProgress = ref(false);
    const data = ref(null);
    const apiPath = ref('');
    const fieldNames = ref({});
    const routeName = ref('');
    const order_by = ref('');
    const order_by_field_name = ref('');

    // Normalize permissions (supports Array and String)
    const normalizePermissions = (permissions) => {
        if (Array.isArray(permissions)) {
            return permissions;
        }

        return permissions ? [permissions] : [];
    };

    // Event handler
    const handleUserRetrieved = () => {
        userPermissions.value = JSON.parse(
            localStorage.getItem('permissions')
        ) || [];
    };

    // Lifecycle: beforeMount
    onBeforeMount(() => {
        requiredPermissionsForView.value = [
            'full-access-and-permissions',
            'view-' + routePrefixName.value
        ];

        // Override default permissions
        const overridePermissions = normalizePermissions(
            props.overrideRequiredPermissionsForView
        );

        if (overridePermissions.length > 0) {
            requiredPermissionsForView.value = [...overridePermissions];
        }

        // Append additional permissions
        const additionalPermissions = normalizePermissions(
            props.additionalRequiredPermissionsForView
        );

        if (additionalPermissions.length > 0) {
            requiredPermissionsForView.value = [
                ...requiredPermissionsForView.value,
                ...additionalPermissions
            ];
        }

        // Listen for user permissions
        Broadcast.$on('User-Retrieved', handleUserRetrieved);

        // Load existing permissions
        const permissions = localStorage.getItem('permissions');

        if (permissions !== null) {
            userPermissions.value = JSON.parse(permissions);
        }
    });

    // Lifecycle: mounted
    onMounted(() => {
        redirectTo.value = route.query.redirect_to;

        if (props.checkForPermissions) {
            const hasPermission = requiredPermissionsForView.value.some(
                permission => userPermissions.value.includes(permission)
            );

            if (!hasPermission) {
                router.push({ name: 'overview' });
            }
        }
    });

    // Cleanup event listener
    onBeforeUnmount(() => {
        Broadcast.$off('User-Retrieved', handleUserRetrieved);
    });

    // Methods
    const getData = (params, dataAttribute) => {
        showProgress.value = true;

        axios.get(apiPath.value, { params })
            .then(res => {
                data.value = res.data;
            })
            .catch(error => {
                if (error.response?.status === 401) {
                    location.reload();
                }

                if (error.response?.status === 500) {
                    alert('Kindly report this issue to the devs.');
                }
            })
            .finally(() => {
                showProgress.value = false;
            });
    };

    const getParameters = () => {
        return fieldNames.value;
    };

    const clearParameters = () => {
        for (const key of Object.keys(fieldNames.value)) {
            fieldNames.value[key] = '';
        }

        router.push({
            name: routeName.value,
            query: {
                page: route.query.page,
                per_page: data.value?.meta?.per_page,
                order_by: order_by.value,
                order_by_field_name: order_by_field_name.value
            }
        });
    };

    const hasCompany = (contact) => {
        if (
            typeof contact?.company_name !== 'object' &&
            typeof contact?.company_name === 'string'
        ) {
            return true;
            // return contact.company_name +
            //     ' <span class="badge text-white bg-purple">Company</span>';
        }

        return false;
        // return contact.name +
        //     ' <span class="badge text-white bg-teal">Person</span>';
    };

    const convertDate = (value) => {
        if (value) {
            return DateTime.fromISO(value).toFormat('(ccc) LL dd, yyyy');
        }
    };

    const convertYearOnly = (value) => {
        if (value) {
            return DateTime.fromISO(value).toFormat('LLLL');
        }
    };

    const convertDateNoYear = (value) => {
        if (value) {
            return DateTime.fromISO(value).toFormat('LLLL dd');
        }
    };

    const convertDateTime = (value) => {
        if (value) {
            return DateTime.fromISO(value).toFormat('(ccc) MMMM dd, yyyy, t');
        }
    };

    return {
        userPermissions,
        requiredPermissionsForView,
        redirectTo,
        routePrefixName,
        showProgress,
        data,
        apiPath,
        fieldNames,
        routeName,
        order_by,
        order_by_field_name,

        getData,
        getParameters,
        clearParameters,
        formatContact,
        convertDate,
        convertYearOnly,
        convertDateNoYear,
        convertDateTime
    };
}
