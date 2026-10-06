<template>
    <form v-on:submit.prevent="create()" autocomplete="off">
        <div v-if="$parent.ifReady">
            <slot name="default"></slot>
        </div>
        <div v-else>
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
            </div>
        </div>

        <br>

        <div v-if="! hideDefaultButtons">
            <button type="button" class="btn btn-outline-secondary btn-sm me-2" @click="$router.back()">
                <i class="fas fa-chevron-left"></i>&nbsp; Back
            </button>

            <button v-if="$parent.ifReady && showCreatePermission && ! disableSaveButton" type="submit" class="btn btn-success btn-sm me-2">
                <i class="fas fa-plus"></i>&nbsp; Save {{ singularName }}
            </button>

            <slot name="other-buttons"></slot>
        </div>
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
            isDisabled: {
                type: Boolean,
                required: false,
                default: false
            },
            hideDefaultButtons: {
                type: Boolean,
                required: false,
                default: false
            },
            returnHere: {
                type: Boolean,
                required: false,
                default: false
            },
            isFormData: {
                type: Boolean,
                required: false,
                default: false
            },
            overrideRequiredPermissionsForCreate: {
                type: [Array, String],
                required: false,
                default: []
            },
            checkForPermissions: {
                type: Boolean,
                required: false,
                default: true
            },
            disableSaveButton: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        data() {
            return {
                redirectTo: null,
                userPermissions: [],
                showCreatePermission: true,
                requiredPermissionsForCreate: null,
            };
        },

        created() {
            this.requiredPermissionsForCreate = ['full-access-and-permssions', 'create-' + this.$parent.routePrefixName];

            if (this.overrideRequiredPermissionsForCreate.length > 0) {
                this.requiredPermissionsForCreate = this.overrideRequiredPermissionsForCreate;
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
                // For Update Permission
                if (Array.isArray(this.requiredPermissionsForCreate)) {
                    if (! this.requiredPermissionsForCreate.some(requiredPermissionForCreate => this.userPermissions.includes(requiredPermissionForCreate) === true)) {
                        this.showCreatePermission = false;
                        this.$router.push({ name: this.$parent.routePrefixName + '.index' });
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForCreate)) {
                        this.showCreatePermission = false;
                        this.$router.push({ name: this.$parent.routePrefixName + '.index' });
                    }
                }
            }
        },

        methods: {
            backToIndex() {
                this.$router.push({ name: `${this.routePrefixName}.index` });
            },
            create() {
                this.$parent.ifReady = false;

                let payload = this.fieldColumns;

                if (this.isFormData) {
                    const formData = new FormData();

                    for (let fieldName in this.fieldColumns) {
                        if (Array.isArray(this.fieldColumns[fieldName])) {
                            this.fieldColumns[fieldName].forEach((item, index) => {
                                formData.append(`${fieldName}[${index}]`, JSON.stringify(item));
                            });
                        } else {
                            formData.append(fieldName, this.fieldColumns[fieldName]);
                        }
                    }

                    payload = formData;
                }

                axios.post(`${this.apiPath}`, payload, {
                    onUploadProgress: uploadEvent => {
                        console.log('Upload Progress: ' + (uploadEvent.loaded / uploadEvent.total * 100) + '%');
                    }
                }).then((response) => {

                    if (response.status === 201) {
                        Broadcast.$emit('ToastMessage', {
                            toastMessage: `${this.toastMessage} created successfully`
                        });
                    }

                    this.clearLocalStorageUsingPrefix(this.$parent.modulePrefix);

                    if (typeof this.redirectTo != 'undefined' && this.redirectTo != '') {
                        this.$router.options.routes.forEach(route => {
                            if (route.name == this.$route.query.redirect_to) {
                                if (Object.keys(this.$route.query).length > 0) {
                                    this.$router.push({
                                        name: this.$route.query.redirect_to,
                                        params: this.$route.query,
                                        query: this.$route.query
                                    });
                                } else {
                                    this.$router.push({ name: this.$route.query.redirect_to });
                                }
                            }
                        });
                    } else if (this.returnHere) {
                        this.$parent.clearFields();
                        this.$parent.ifReady = true;
                    } else {
                        this.$router.push({ name: `${this.routePrefixName}.index` });
                    }
                }).catch(err => {
                    console.log(err);
                    this.$parent.ifReady = true;
                });
            },
            clearLocalStorageUsingPrefix(prefix) {
                // 1. Get all keys currently in localStorage
                const keysToRemove = [];

                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    // 2. Check if the key starts with the specified prefix
                    if (key.startsWith(prefix)) {
                        keysToRemove.push(key);
                    }
                }

                // 3. Iterate through the matching keys and remove them one by one
                for (const key of keysToRemove) {
                    localStorage.removeItem(key);
                }
            }
        }
    }
</script>
