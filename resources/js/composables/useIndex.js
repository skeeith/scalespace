import { ref, onBeforeMount, onMounted, onBeforeUnmount } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import { DateTime } from 'luxon';

export function index(props) {
    const router = useRouter();
    const route = useRoute();

    const userPermissions = ref([]);
    const requiredPermissionsForView = ref([]);
    const redirectTo = ref(null);

    const apiPath = ref('');
    const routePrefixName = ref('');
    const routeName = ref('');
    
    const data = ref({});
    const fieldNames = ref({});

    const order_by = ref('');
    const order_by_field_name = ref('');

    const showProgress = ref(false);

    const normalizePermissions = (permissions) => {
        if (Array.isArray(permissions)) {
            return permissions;
        }

        return permissions ? [permissions] : [];
    };

    const handleUserRetrieved = () => {
        userPermissions.value = JSON.parse(localStorage.getItem('permissions')) || [];
    };

    onBeforeMount(() => {
        requiredPermissionsForView.value = [
            'full-access-and-permissions',
            'view-' + routePrefixName.value
        ];

        const overridePermissions = normalizePermissions(props.overrideRequiredPermissionsForView);

        if (overridePermissions.length > 0) {
            requiredPermissionsForView.value = [...overridePermissions];
        }

        const additionalPermissions = normalizePermissions(props.additionalRequiredPermissionsForView);

        if (additionalPermissions.length > 0) {
            requiredPermissionsForView.value = [
                ...requiredPermissionsForView.value,
                ...additionalPermissions
            ];
        }

        Broadcast.$on('User-Retrieved', handleUserRetrieved);

        const permissions = localStorage.getItem('permissions');

        if (permissions !== null) {
            userPermissions.value = JSON.parse(permissions);
        }
    });

    onMounted(() => {
        redirectTo.value = route.query.redirect_to;

        if (props.checkForPermissions) {
            const hasPermission = requiredPermissionsForView.value.some(permission => userPermissions.value.includes(permission));

            if (! hasPermission) {
                router.push({ name: 'overview' });
            }
        }
    });

    // Cleanup event listener
    // onBeforeUnmount(() => {
    //     Broadcast.$off('User-Retrieved', handleUserRetrieved);
    // });

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
        }

        return false;
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
        clearParameters,
        hasCompany,
        convertDate,
        convertYearOnly,
        convertDateNoYear,
        convertDateTime
    };
}
