<template>
    <!-- Title -->
    <slot name="title"></slot>

    <!-- Table -->
    <slot name="table"></slot>

    <!-- Pagination -->
    <!-- <pagination v-if="$parent.data" :meta="$parent.data.meta" :links="$parent.data.links" :routeName="$parent.routeName"></pagination> -->

    <!-- Search Modal -->
    <slot name="search-modal"></slot>
</template>

<script>
    export default {
        props: {
            apiPath: String,
            routePrefixName: String,
            pluralName: String,
            toastMessage: String,
            dataAttribute: String,
            overrideRequiredPermissionsForView: {
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
            console.log('this.$parent.routePrefixName', this.$parent.routePrefixName);
            return {
                userPermissions: [],
                requiredPermissionsForView: ['full-access-and-permissions', 'view-' + this.$parent.routePrefixName],
            }
        },

        created() {
            if (this.overrideRequiredPermissionsForView.length > 0) {
                this.requiredPermissionsForView = this.overrideRequiredPermissionsForView;
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
                // For View Permission
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

        beforeRouteEnter(to, from, next) {
            if (to.query.per_page == null) {
                next(vm => vm.getData({
                    page:         to.query.page,
                    per_page:     15,
                    order_by:     to.query.order_by,
                    only_trashed: to.query.only_trashed,
                    with_trashed: to.query.with_trashed,
                }));
            } else {
                next(vm => vm.getData({
                    page:         to.query.page,
                    per_page:     to.query.per_page,
                    order_by:     to.query.order_by,
                    only_trashed: to.query.only_trashed,
                    with_trashed: to.query.with_trashed,
                }));
            }
        },

        beforeRouteUpdate(to, from, next) {
            this.getData({
                page:         to.query.page,
                per_page:     this.data.meta.per_page,
                order_by:     to.query.order_by,
                only_trashed: to.query.only_trashed,
                with_trashed: to.query.with_trashed,
            });

            next();
        },

        methods: {
            getData(params) {
                this.showProgress  = true;

                (typeof params.order_by === 'undefined' || params.order_by == 'desc') ? this.order_by = 'desc' : this.order_by = 'asc';

                axios.get(this.apiPath, { params }).then(res => {
                    console.log('Form Index res.data', res.data);
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
                // return {
                //     name: this.name,
                // };
            },
            clearParameters() {
                this.name         = '';
                this.display_name = '';
                this.order_by     = 'desc';

                // for (const [key, value] of Object.entries(this.parameters)) {
                //     if (! this.ignoreFields.includes(key)) {
                //         this.parameters[key] = this.$data.searchAllField;
                //     }
                // }

                // this.$parent.showProgress = true;
                // this.parameters.with_trashed = value == 1 ? 0 : 1;
                // this.parameters.only_trashed = 0;

                // this.$router.push({
                //     name: this.routeName,
                //     query: {
                //         page: 1,
                //         per_page: this.data.meta.per_page,
                //         ...this.parameters
                //     }
                // });
            }
        }
    }
</script>
