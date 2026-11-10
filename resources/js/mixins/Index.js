export default {
    props: {
        overrideRequiredPermissionsForView: {
            type: [Array, String],
            required: false,
            default: []
        },
        additionalRequiredPermissionsForView: {
            type: [Array, String],
            required: false,
            default: []
        },
        checkForPermissions: {
            type: Boolean,
            required: false,
            default: true
        }
    },

    data() {
        return {
            userPermissions: [],
            requiredPermissionsForView: null,
        }
    },

    created() {
        this.requiredPermissionsForView = ['full-access-and-permissions', 'view-' + this.$data.routePrefixName];

        if (this.overrideRequiredPermissionsForView.length > 0) {
            this.requiredPermissionsForView = this.overrideRequiredPermissionsForView;
        }

        if (this.additionalRequiredPermissionsForView.length > 0) {
            this.requiredPermissionsForView = [...this.additionalRequiredPermissionsForView];
        }

        Broadcast.$on('User-Retrieved', (event) => {
            this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
        });

        if (localStorage.getItem('permissions') !== null) {
            this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
        }
    },

    mounted() {
        this.redirectTo = this.$route.query.redirect_to;

        if (this.checkForPermissions) {
            if (Array.isArray(this.requiredPermissionsForView)) {
                if (! this.requiredPermissionsForView.some(requiredPermissionForView => this.userPermissions.includes(requiredPermissionForView) === true)) {
                    this.$router.push({ name: 'overview' });
                }
            } else {
                if (! this.userPermissions.includes(requiredPermissionsForView)) {
                    this.$router.push({ name: 'overview' });
                }
            }
        }
    },

    methods: {
        getData(params, dataAttribute) {
            this.showProgress  = true;

            axios.get(this.apiPath, { params }).then(res => {
                this.data = res.data;

                this.showProgress = false;
            }).catch(error => {
                if (error.response.status == 401) {
                    location.reload();
                }

                if (error.response.status == 500) {
                    alert('Kindly report this issue to the devs.');
                }
            });
        },
        getParameters() {
            return this.fieldNames;
        },
        clearParameters() {
            for (const [key, value] of Object.entries(this.fieldNames)) {
                this.fieldNames[key] = '';
            }

            this.$router.push({
                name: this.routeName,
                query: {
                    page: this.$route.query.page,
                    per_page: this.data.meta.per_page,
                    order_by: this.order_by,
                    order_by_field_name: this.order_by_field_name
                }
            });
        },
        formatContact(contact) {
            if (typeof contact?.company_name !== 'object' && typeof contact?.company_name === 'string') {
                return contact.company_name + ' <span class="badge text-white bg-purple">Company</span>';
            } else {
                return contact.name + ' <span class="badge text-white bg-teal">Person</span>';
            }
        },
        convertDate(value) {
            if (value) {
                return DateTime.fromISO(value).toFormat('(ccc) LL dd, yyyy');
            }

            return;
        },
        convertYearOnly(value) {
            if (value) {
                return DateTime.fromISO(value).toFormat('LLLL');
            }

            return;
        },
        convertDateNoYear(value) {
            if (value) {
                return DateTime.fromISO(value).toFormat('LLLL dd');
            }

            return;
        },
        convertDateTime(value) {
            if (value) {
                return DateTime.fromISO(value).toFormat("(ccc) MMMM dd, yyyy, t");
            }

            return;
        }
    }
}
