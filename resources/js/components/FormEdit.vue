<template>
    <form v-on:submit.prevent="update" autocomplete="off">
        <div v-if="$parent.ifReady">
            <slot name="default"></slot>
        </div>
        <div v-else>
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
            </div>
        </div>

        <br>

        <button type="button" class="btn btn-outline-secondary btn-sm me-2" @click="$router.back()">
            <i class="fas fa-chevron-left"></i>&nbsp; Back
        </button>
        <button v-if="$parent.ifReady && showUpdatePermission && ! disableUpdateButton" type="submit" class="btn btn-success btn-sm me-2">
            <i class="fas fa-edit"></i>&nbsp; Update {{ singularName }}
        </button>

        <slot name="other-buttons"></slot>
    </form>
</template>

<script>
    export default {
        props: {
            apiPath: String,
            routePrefixName: String,
            singularName: String,
            fieldColumns: null,
            toastMessage: String,
            isFormData: {
                type: Boolean,
                required: false,
                default: false,
            },
            routeParameter: {
                type: String,
                required: false,
                default: 'id',
            },
            overrideRequiredPermissionsForUpdate: {
                type: [Array, String],
                required: false,
                default: [],
            },
            checkForPermissions: {
                type: Boolean,
                required: false,
                default: true,
            },
            disableUpdateButton: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        data() {
            return {
                userPermissions: [],
                showUpdatePermission: true,
                requiredPermissionsForUpdate: null,
            }
        },

        created() {
            this.requiredPermissionsForUpdate = ['full-access-and-permssions', 'update-' + this.$parent.routePrefixName];

            if (this.overrideRequiredPermissionsForUpdate.length > 0) {
                this.requiredPermissionsForUpdate = this.overrideRequiredPermissionsForUpdate;
            }

            Broadcast.$on('User-Retrieved', (event) => {
                this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
            });

            if (localStorage.getItem('permissions') !== null) {
                this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
            }
        },

        mounted() {
            if (this.checkForPermissions) {
                // For Update Permission
                if (Array.isArray(this.requiredPermissionsForUpdate)) {
                    if (! this.requiredPermissionsForUpdate.some(requiredPermissionForUpdate => this.userPermissions.includes(requiredPermissionForUpdate) === true)) {
                        this.showUpdatePermission = false;
                        this.$router.push({ name: this.$parent.routePrefixName + '.index' });
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForUpdate)) {
                        this.showUpdatePermission = false;
                        this.$router.push({ name: this.$parent.routePrefixName + '.index' });
                    }
                }
            }
        },

        methods: {
            backToIndex() {
                this.$router.push({ name: `${this.routePrefixName}.index` });
            },
            update() {
                this.$parent.ifReady = false;

                let payload = this.fieldColumns;

                if (this.isFormData) {
                    const formData = new FormData();

                    for (let fieldName in this.fieldColumns) {
                        formData.append(fieldName, this.fieldColumns[fieldName]);
                    }

                    formData.append('_method', 'PATCH');

                    payload = formData;

                    axios.post(`${this.apiPath}` + '/' + this.$route.params[this.routeParameter], payload, {
                        onUploadProgress: uploadEvent => {
                            console.log('Upload Progress: ' + (uploadEvent.loaded / uploadEvent.total * 100) + '%');
                        }
                    }).then((response) => {
                        if (response.status === 200) {
                            Broadcast.$emit('ToastMessage', {
                                toastMessage: `${this.toastMessage} updated successfully.`
                            });
                        }

                        this.redirectTo();
                    }).catch(err => {
                        this.$parent.ifReady = true;
                        console.log(err);
                    });
                } else {
                    axios.patch(`${this.apiPath}` + '/' + this.$route.params[this.routeParameter], payload).then((response) => {
                        if (response.status === 200) {
                            Broadcast.$emit('ToastMessage', {
                                toastMessage: `${this.toastMessage} updated successfully.`
                            });
                        }

                        this.redirectTo();
                    }).catch(err => {
                        this.$parent.ifReady = true;
                        console.log(err);
                    });
                }
            },
            redirectTo() {
                if (typeof this.$route.query.redirect_to != 'undefined' && this.$route.query.redirect_to != '') {
                    this.$router.options.routes.forEach(route => {
                        if (route.name === this.$route.query.redirect_to) {
                            this.$router.push({ name: this.$route.query.redirect_to });
                        }
                    });
                }

                this.$router.push({
                    name: `${this.routePrefixName}.view`,
                    params: { id: this.$route.params.id }
                });
            }
        }
    }
</script>
