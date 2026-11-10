<template>
    <div>
        <router-link v-if="! disableView && showViewPermission" class="text-secondary" :to="routeToView">
            <i class="fas fa-eye"></i>
            &nbsp;
            <strong>View</strong>
        </router-link>

        <span v-if="! disableView && ! disableEdit && showUpdatePermission"><strong>&nbsp; | &nbsp;</strong></span>

        <router-link v-if="! disableEdit && showUpdatePermission" class="text-secondary" :to="routeToEdit">
            <i class="fas fa-edit"></i>
            &nbsp;
            <strong>Edit</strong>
        </router-link>

        <span v-if="$slots.default && ! disableButtonSplitter"></span>

        <slot name="default"></slot>
    </div>
</template>

<script>
    export default {
        props: {
            routePrefixName: String,
            object: Object,
            disableButtonSplitter: {
                type: Boolean,
                required: false,
                default: false
            },
            disableView: {
                type: Boolean,
                required: false,
                default: false
            },
            disableEdit: {
                type: Boolean,
                required: false,
                default: false
            },
            overrideRequiredPermissionsForView: {
                type: [Array, String],
                required: false,
                default: []
            },
            overrideRequiredPermissionsForUpdate: {
                type: [Array, String],
                required: false,
                default: []
            },
            checkForPermissions: {
                type: Boolean,
                required: false,
                default: true
            },
            isNested: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        data() {
            return {
                userPermissions: [],
                showViewPermission: true,
                requiredPermissionsForView: ['full-access-and-permissions', 'view-' + this.$parent.$parent.routePrefixName],
                showUpdatePermission: true,
                requiredPermissionsForUpdate: ['full-access-and-permissions', 'update-' + this.$parent.$parent.routePrefixName],
            };
        },

        computed: {
            routeToView() {
                if (this.isNested) {
                    return {
                        name: `${this.routePrefixName}.view`,
                        params: {
                            recordId: this.object.record_id,
                            fileId: this.object.id
                        }
                    };
                }

                return { name: `${this.routePrefixName}.view`, params: { id: this.object.id }};
            },
            routeToEdit() {
                if (this.isNested) {
                    return {
                        name: `${this.routePrefixName}.edit`,
                        params: {
                            recordId: this.object.record_id,
                            fileId: this.object.id
                        }
                    };
                }

                return { name: `${this.routePrefixName}.edit`, params: { id: this.object.id }};
            },
        },

        mounted() {
            if (this.overrideRequiredPermissionsForView.length > 0) {
                this.requiredPermissionsForView = this.overrideRequiredPermissionsForView;
            }

            if (this.overrideRequiredPermissionsForUpdate.length > 0) {
                this.requiredPermissionsForUpdate = this.overrideRequiredPermissionsForUpdate;
            }

            Broadcast.$on('User-Retrieved', (event) => {
                this.ifReady = false;
                this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
                this.ifReady = true;
            });

            if (localStorage.getItem('permissions') !== null) {
                this.ifReady = false;
                this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
                this.ifReady = true;
            }

            if (this.checkForPermissions) {
                // For View Individual
                if (Array.isArray(this.requiredPermissionsForView)) {
                    if (! this.requiredPermissionsForView.some(requiredPermissionForView => this.userPermissions.includes(requiredPermissionForView) === true)) {
                        this.showViewPermission = false;
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForView)) {
                        this.showViewPermission = false;
                    }
                }

                // For Update
                if (Array.isArray(this.requiredPermissionsForUpdate)) {
                    if (! this.requiredPermissionsForUpdate.some(requiredPermissionForUpdate => this.userPermissions.includes(requiredPermissionForUpdate) === true)) {
                        this.showUpdatePermission = false;
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForUpdate)) {
                        this.showUpdatePermission = false;
                    }
                }
            }
        }
    }
</script>

<style>
    a {
        text-decoration: none;
    }
</style>
