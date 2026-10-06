<template>
    <div v-if="$parent.ifReady" autocomplete="off">
        <fieldset :disabled="disableFieldset">
            <slot name="default"></slot>
        </fieldset>
    </div>
    <div v-else>
        <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
        </div>
    </div>

    <br>

    <button v-if="! hideDefaultButtons" class="btn btn-outline-secondary btn-sm me-2" @click.prevent="$router.back()">
        <i class="fas fa-chevron-left"></i>&nbsp; Back
    </button>

    <button v-if="($parent.ifReady && ! disableEdit && showUpdatePermission && ! $parent.object['deleted_at']) && ! hideDefaultButtons" class="btn btn-primary btn-sm me-2" @click.prevent="edit()">
        <i class="fas fa-edit"></i>&nbsp; Edit {{ singularName }}
    </button>

    <button v-if="($parent.ifReady && ! disableDelete && showDeletePermission && ! $parent.object['deleted_at']) && ! hideDefaultButtons" type="button" class="btn btn-danger btn-sm me-2" @click.prevent="openModal('delete')">
        <i class="fas fa-trash-alt"></i>&nbsp; Delete {{ singularName }}
    </button>

    <button v-if="($parent.ifReady && ! disableDelete && showRestorePermission && $parent.object['deleted_at']) && ! hideDefaultButtons" type="button" class="btn btn-success btn-sm me-2" @click.prevent="openModal('restore')">
        <i class="fas fa-recycle"></i>&nbsp; Restore {{ singularName }}
    </button>

    <button v-if="($parent.ifReady && ! disableDelete && showForceDeletePermission && $parent.object['deleted_at']) && ! hideDefaultButtons" type="button" class="btn btn-danger btn-sm me-2" @click.prevent="openModal('permanently delete')">
        <i class="fas fa-trash-alt"></i>&nbsp; Delete {{ singularName }} Permanently
    </button>

    <button v-if="$parent.ifReady && enableResetPassword" class="btn btn-warning btn-sm me-2 float-end" @click.prevent="openModal('reset password for')">
        <i class="fas fa-refresh"></i>&nbsp; Reset {{ singularName }} Password
    </button>

    <button v-if="$parent.ifReady && enableResetSecurityPin" class="btn btn-warning btn-sm me-2 float-end" @click.prevent="openModal('reset security pin for')">
        <i class="fas fa-refresh"></i>&nbsp; Reset {{ singularName }} Security Pin
    </button>

    <slot name="other-buttons"></slot>

    <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="delete-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="delete-modal-title">You're about to {{ modalTitle }} this.</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div v-if="$parent.ifReady" class="modal-body">
                    <div v-if="Object.keys($parent.object).length !== 0">
                        Are you sure you want to {{ modalTitle }} {{ singularName }} {{ $parent.object[selectedProperty] }}?
                    </div>
                    <div v-else>
                        Are you sure you want to {{ modalTitle }} {{ singularName }} {{ selectedProperty }}?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm me-2" v-show="modalTitle == 'delete'" @click.prevent="deleteItem()">Confirm Delete</button>
                    <button type="button" class="btn btn-success btn-sm me-2" v-show="modalTitle ==  'restore'" @click.prevent="restoreItem()">Restore</button>
                    <button type="button" class="btn btn-danger btn-sm me-2" v-show="modalTitle == 'permanently delete'" @click.prevent="deleteItemPermanently()">Confirm Delete Permanently</button>
                    <button type="button" class="btn btn-warning btn-sm me-2" v-show="modalTitle == 'reset password for'" @click.prevent="resetPassword()">Confirm Reset Password</button>
                    <button type="button" class="btn btn-warning btn-sm me-2" v-show="modalTitle == 'reset security pin for'" @click.prevent="resetSecurityPin()">Confirm Reset PIN</button>
                    <button type="button" class="btn btn-secondary btn-sm me-2" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            apiPath: String,
            routePrefixName: String,
            singularName: String,
            toastMessage: String,
            selectedProperty: {
                type: String,
                required: false,
                default: null
            },
            disableFieldset: {
                type: Boolean,
                required: false,
                default: true
            },
            hideDefaultButtons: {
                type: Boolean,
                required: false,
                default: false
            },
            disableEdit: {
                type: Boolean,
                required: false,
                default: false
            },
            disableDelete: {
                type: Boolean,
                required: false,
                default: false
            },
            enableResetPassword: {
                type: Boolean,
                required: false,
                default: false
            },
            enableResetSecurityPin: {
                type: Boolean,
                required: false,
                default: false
            },
            isFormReadySelector: {
                type: String,
                required: false
            },
            overrideRequiredPermissionsForView: {
                type: [Array, String],
                required: false,
                default: [],
            },
            overrideRequiredPermissionsForUpdate: {
                type: [Array, String],
                required: false,
                default: [],
            },
            overrideRequiredPermissionsForDelete: {
                type: [Array, String],
                required: false,
                default: [],
            },
            overrideRequiredPermissionsForRestore: {
                type: [Array, String],
                required: false,
                default: [],
            },
            overrideRequiredPermissionsForForceDelete: {
                type: [Array, String],
                required: false,
                default: [],
            },
            checkForPermissions: {
                type: Boolean,
                required: false,
                default: true
            },
        },

        data() {
            return {
                modal: {},
                modalTitle: '',
                userPermissions: [],
                requiredPermissionsForView: null,
                showUpdatePermission: true,
                requiredPermissionsForUpdate: null,
                showDeletePermission: true,
                requiredPermissionsForDelete: null,
                showRestorePermission: true,
                requiredPermissionsForRestore: null,
                showForceDeletePermission: true,
                requiredPermissionsForForceDelete: null,
            }
        },

        created() {
            this.requiredPermissionsForView = ['full-access-and-permssions', 'view-' + this.$parent.routePrefixName];
            this.requiredPermissionsForUpdate = ['full-access-and-permssions', 'update-' + this.$parent.routePrefixName];
            this.requiredPermissionsForDelete = ['full-access-and-permssions', 'delete-' + this.$parent.routePrefixName];
            this.requiredPermissionsForRestore = ['full-access-and-permssions', 'restore-' + this.$parent.routePrefixName];
            this.requiredPermissionsForForceDelete = ['full-access-and-permssions', 'force-delete-' + this.$parent.routePrefixName];

            if (this.overrideRequiredPermissionsForView.length > 0) {
                this.requiredPermissionsForView = this.overrideRequiredPermissionsForView;
            }

            if (this.overrideRequiredPermissionsForUpdate.length > 0) {
                this.requiredPermissionsForUpdate = this.overrideRequiredPermissionsForUpdate;
            }

            if (this.overrideRequiredPermissionsForDelete.length > 0) {
                this.requiredPermissionsForDelete = this.overrideRequiredPermissionsForDelete;
            }

            if (this.overrideRequiredPermissionsForRestore.length > 0) {
                this.requiredPermissionsForRestore = this.overrideRequiredPermissionsForRestore;
            }

            if (this.overrideRequiredPermissionsForForceDelete.length > 0) {
                this.requiredPermissionsForForceDelete = this.overrideRequiredPermissionsForForceDelete;
            }

            Broadcast.$on('User-Retrieved', (event) => {
                this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
            });

            if (localStorage.getItem('permissions') !== null) {
                this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
            }
        },

        mounted() {
            this.modal = new bootstrap.Modal(`#delete-modal`, { backdrop: "static ", keyboard: false } );

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

                // For Update Permission
                if (Array.isArray(this.requiredPermissionsForUpdate)) {
                    if (! this.requiredPermissionsForUpdate.some(requiredPermissionForUpdate => this.userPermissions.includes(requiredPermissionForUpdate) === true)) {
                        this.showUpdatePermission = false;
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForUpdate)) {
                        this.showUpdatePermission = false;
                    }
                }

                // For Delete Permission
                if (Array.isArray(this.requiredPermissionsForDelete)) {
                    if (! this.requiredPermissionsForDelete.some(requiredPermissionForDelete => this.userPermissions.includes(requiredPermissionForDelete) === true)) {
                        this.showDeletePermission = false;
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForDelete)) {
                        this.showDeletePermission = false;
                    }
                }

                // For Restore Permission
                if (Array.isArray(this.requiredPermissionsForRestore)) {
                    if (! this.requiredPermissionsForRestore.some(requiredPermissionForRestore => this.userPermissions.includes(requiredPermissionForRestore) === true)) {
                        this.showRestorePermission = false;
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForRestore)) {
                        this.showRestorePermission = false;
                    }
                }

                // For Force Delete Permission
                if (Array.isArray(this.requiredPermissionsForForceDelete)) {
                    if (! this.requiredPermissionsForForceDelete.some(requiredPermissionForForceDelete => this.userPermissions.includes(requiredPermissionForForceDelete) === true)) {
                        this.showForceDeletePermission = false;
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForForceDelete)) {
                        this.showForceDeletePermission = false;
                    }
                }
            }
        },

        methods: {
            openModal(title) {
                this.modalTitle = title;

                this.modal.show();
            },
            backToIndex() {
                this.$router.push({ name: `${this.routePrefixName}.index` });
            },
            edit() {
                this.$router.push({ name: `${this.routePrefixName}.edit`, params: { id: this.$route.params.id } });
            },
            deleteItem() {
                this.modal.hide();

                this.updateIfReady(false);

                axios.delete(`${this.apiPath}` + '/' + this.$route.params.id).then(() => {
                    Broadcast.$emit('ToastMessage', {
                        toastMessage: `${this.toastMessage} deleted successfully`
                    });

                    this.$router.push({ name: `${this.routePrefixName}.index` });
                }).catch(err => {
                    console.log(err);
                    
                    this.updateIfReady(true);
                });
            },
            restoreItem() {
                this.modal.hide();

                this.updateIfReady(false);

                axios.patch(`${this.apiPath}` + '/' + this.$route.params.id + '/restore').then(() => {
                    Broadcast.$emit('ToastMessage', {
                        toastMessage: `${this.toastMessage} restored successfully`
                    });

                    this.$router.push({ name: `${this.routePrefixName}.index` });
                }).catch(err => {
                    console.log(err);
                    
                    this.updateIfReady(true);
                });
            },
            deleteItemPermanently() {
                this.modal.hide();

                this.updateIfReady(false);

                axios.delete(`${this.apiPath}` + '/' + this.$route.params.id + '/force-delete').then(() => {
                    Broadcast.$emit('ToastMessage', {
                        toastMessage: `${this.toastMessage} permanently deleted`
                    });

                    this.$router.push({ name: `${this.routePrefixName}.index` });
                }).catch(err => {
                    console.log(err);
                    
                    this.updateIfReady(true);
                });
            },
            resetSecurityPin() {
                this.modal.hide();

                axios.post(`${this.apiPath}` + '/reset-security-pin', { id: this.$route.params.id }).then(() => {
                    Broadcast.$emit('ToastMessage', {
                        toastMessage: `${this.toastMessage} reset security pin successfully`
                    });

                }).catch(err => {
                    console.log(err);
                    this.updateIfReady(true);
                });
            },
            resetPassword() {
                this.modal.hide();

                axios.post(`${this.apiPath}` + '/reset-password', { id: this.$route.params.id } ).then(() => {
                    Broadcast.$emit('ToastMessage', {
                        toastMessage: `${this.toastMessage} reset password successfully`
                    });
                }).catch(err => {
                    console.log(err);
                    this.updateIfReady(true);
                });
            },
            updateIfReady(boolean) {
                if (typeof this.isFormReadySelector !== 'undefined') {
                    this.$parent[this.isFormReadySelector].ifReady = boolean;
                } else {
                    this.$parent.ifReady = boolean;
                }
            }
        }
    }
</script>
