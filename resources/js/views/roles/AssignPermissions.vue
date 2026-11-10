<template>
    <div>
        <div class="card">
            <div class="card-body">
                <form-title :routePrefixName="routePrefixName" :title="title" v-bind:showRightSide="false"></form-title>
                <br>
                <div v-if="ifReady">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input id="name" type="text" class="form-control" v-model="role.name" disabled>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group mb-3">
                                <label for="display-name" class="form-label">Display Name</label>
                                <input id="display-name" class="form-control" type="text" v-model="role.display_name" disabled>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" class="form-control" v-model="role.description" rows="3" maxlength="500" disabled></textarea>
                            </div>
                        </div>
                    </div>

                    <fieldset v-for="(modulePermissions, permissionModuleName, permissionModuleIndex) in permissions" :key="permissionModuleIndex">
                        <legend class="mb-3">{{ permissionModuleName }}</legend>
                        <div class="row">
                            <div v-for="(permission, permissionIndex) in modulePermissions" :key="permissionIndex" class="col-lg-4">
                                <div v-show="permission.is_requesting">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                    </div>
                                </div>
                                <div v-show="! permission.is_requesting" class="clear-fix">
                                    <label class="me-3 mb-3 float-start"><strong>{{ permission.display_name }}</strong></label>
                                    <div class="btn-group btn-group-toggle float-end" data-toggle="buttons">
                                        <label :id="'unassigned-permission-' + permissionModuleName + '-' + permissionIndex" class="btn btn-outline-danger btn-sm" @click="togglePermission(permissionModuleName, permissionIndex, false)" :class="{ active: ! permission.is_assigned }">
                                            Unassigned
                                        </label>
                                        <label :id="'assigned-permission-' + permissionModuleName + '-' + permissionIndex" class="btn btn-outline-success btn-sm" @click="togglePermission(permissionModuleName, permissionIndex, true)" :class="{ active: permission.is_assigned }">
                                            Assigned
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr v-if="permissionModuleIndex != Object.keys(permissions).length - 1">
                        <br v-if="permissionModuleIndex != Object.keys(permissions).length - 1">
                    </fieldset>

                    <br>

                    <button class="btn btn-outline-secondary btn-sm me-2" @click="$router.back()">
                        <i class="fas fa-chevron-left"></i>&nbsp; Back
                    </button>
                </div>

                <div v-else>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                ifReady:         false,
                data:            null,
                action:          'View',
                singularName:    'Role',
                title:           'Assign Permissions to a Role',
                pluralName:      'Roles',
                apiPath:         '/api/roles',
                routeName:       'roles.index',
                routePrefixName: 'roles',
                useName:         'plural',
                showProgress:    false,
                order_by:        'desc',

                userPermissions: [],
                showAssignPermissionsForRolesPermission: true,
                requiredPermissionsForAssignPermissionsForRolesPermission: ['full-access-and-permissions', 'assign-permissions-for-roles'],
                checkForPermissions: true,

                permissions:         null,
                assignedPermissions: null,
                role:                null,
            };
        },

        created() {
            Broadcast.$on('User-Retrieved', (event) => {
                this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
            });

            if (localStorage.getItem('permissions') !== null) {
                this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
            }

            if (this.checkForPermissions) {
                if (Array.isArray(this.requiredPermissionsForAssignPermissionsForRolesPermission)) {
                    if (! this.requiredPermissionsForAssignPermissionsForRolesPermission.some(requiredPermissionForAssignPermissionsForRolesPermission => this.userPermissions.includes(requiredPermissionForAssignPermissionsForRolesPermission) === true)) {
                        this.$router.push({ name: this.routeName });
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForAssignPermissionsForRolesPermission)) {
                        this.$router.push({ name: this.routeName });
                    }
                }
            }
        },

        mounted() {
            let retrievePermissionsPromise = new Promise((resolve, reject) => {
                axios.get('/api/permissions/retrieve-all-permissions').then(res => {
                    this.permissions = res.data.permissions;

                    resolve();
                }).catch(err => {
                    console.log(err);
                });
            });

            let retrieveAssignedPermissions = new Promise((resolve, reject) => {
                axios.get('/api/roles/' + this.$route.params.id + '/assigned-permissions').then(res => {
                    this.assignedPermissions = res.data.assignedPermissions;
                    resolve();
                }).catch(err => {
                    console.log(err);
                });
            });

            let retrieveRolePromise = new Promise((resolve, reject) => {
                axios.get('/api/roles/' + this.$route.params.id).then(res => {
                    this.role = res.data.role;
                    resolve();
                }).catch(err => {
                    console.log(err);
                });
            });

            Promise.all([retrievePermissionsPromise, retrieveAssignedPermissions, retrieveRolePromise]).then(() => {
                for (const [permissionModuleName, permissions] of Object.entries(this.permissions)) {
                    permissions.find(permission => {
                        if (_.some(this.assignedPermissions, { id: permission.id })) {
                            permission.is_assigned = true;
                        } else {
                            permission.is_assigned = false;
                        }

                        permission.requesting = false;
                    });
                }

                this.ifReady = true;
            });
        },

        methods: {
            isRequesting(permissionModuleName, permissionIndex, toggleValue) {
                this.permissions[permissionModuleName][permissionIndex].is_requesting = toggleValue;
            },
            togglePermission(permissionModuleName, permissionIndex, toggle) {
                this.isRequesting(permissionModuleName, permissionIndex, true);

                if (toggle) {
                    document.getElementById('assigned-permission-' + permissionModuleName + '-' + permissionIndex).classList.add('active');
                    document.getElementById('unassigned-permission-' + permissionModuleName + '-' + permissionIndex).classList.remove('active');
                } else {
                    document.getElementById('unassigned-permission-' + permissionModuleName + '-' + permissionIndex).classList.add('active');
                    document.getElementById('assigned-permission-' + permissionModuleName + '-' + permissionIndex).classList.remove('active');
                }

                let data = {
                    role_id: this.$route.params.id,
                    permission_id: this.permissions[permissionModuleName][permissionIndex].id
                };

                axios.post('/api/roles/toggle-permission', data).then(response => {
                    this.isRequesting(permissionModuleName, permissionIndex, false);

                    let permissions = JSON.parse(localStorage.getItem('permissions'));
                    let permissionName = this.permissions[permissionModuleName][permissionIndex].name;

                    if (toggle) {
                        permissions.push(permissionName);
                        localStorage.setItem('permissions', JSON.stringify(permissions));
                    } else {
                        permissions.forEach((permission, permissionIndex) => {
                            if (this.permissions[permissionModuleName][permissionIndex].name === permission) {
                                permissions.splice(permissionIndex, 1);
                            }
                        });

                        localStorage.setItem('permissions', JSON.stringify(permissions));
                    }
                }).catch(err => {
                    console.log(err);
                });
            }
        }
    }
</script>

<style>
    .row .col-md-6:first-child {
        border-right: 1px solid rgba(0, 0, 0, 0.125);
    }
</style>
