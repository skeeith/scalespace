<template>
    <div>
        <div class="card">
            <div class="card-body">
                <form-title :routePrefixName="routePrefixName" :title="title" v-bind:showRightSide="false"></form-title>
                <br>
                <div v-if="ifReady">
                    <div class="row">
                        <div class="col-md-6">
                            <fieldset disabled>
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input id="email" type="text" class="form-control" v-model="user.email">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="name" class="form-control" v-model="user.name">
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-3">List of Assignable Roles</h4>
                            <ul class="list-group list-group-flush">
                                <li v-for="(role, index) in roles" class="list-group-item" :key="index">
                                    <div class="row clearfix">
                                        <div class="col float-left">
                                            {{ role.display_name }}
                                        </div>
                                        <div class="col float-right">
                                            <div v-show="role.requesting">
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                                </div>
                                            </div>
                                            <div v-show="! role.requesting">
                                                <div class="btn-group btn-group-toggle float-right" data-toggle="buttons">
                                                    <label :id="'UnassignedRole-'+index" class="btn btn-outline-danger btn-sm" @click="toggleRole(index,false)" :class="{ active: ! role.isAssigned }">
                                                        Unassigned
                                                    </label>
                                                    <label :id="'AssignedRole-'+index" class="btn btn-outline-success btn-sm" @click="toggleRole(index,true)" :class="{ active: role.isAssigned }">
                                                        Assigned
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <br>

                    <button class="btn btn-outline-secondary btn-sm me-2" @click.prevent="viewUsers()">
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
                singularName:    'User',
                title:           'Assign Roles to an User',
                pluralName:      'Admins',
                apiPath:         '/api/users',
                routeName:       'users.index',
                routePrefixName: 'users',
                useName:         'plural',
                showProgress:    false,
                order_by:        'desc',

                userPermissions: [],
                showAssignRolesForUsersPermission: true,
                requiredPermissionsForAssignRolesForUsers: ['full-access-and-permssions', 'assign-roles-users'],
                checkForPermissions: true,

                name:          null,
                email:         null,
                mobile_number: null,
                roles:         null,
                assignedRoles: null,
                user:          null,
            };
        },

        created() {
            let userRoles = JSON.parse(localStorage.getItem('roles'));

            if ((parseInt(this.$route.params.id) === 1 || this.$route.params.id == 1) && ! userRoles.includes('super-administrator')) {
                this.$router.push({ name: `users.index` });
            }

            Broadcast.$on('User-Retrieved', (event) => {
                this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
            });

            if (localStorage.getItem('permissions') !== null) {
                this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
            }

            if (this.checkForPermissions) {
                // For Create Permission
                if (Array.isArray(this.requiredPermissionsForAssignRolesForUsers)) {
                    if (! this.requiredPermissionsForAssignRolesForUsers.some(requiredPermissionForAssignRolesForUsers => this.userPermissions.includes(requiredPermissionForAssignRolesForUsers) === true)) {
                        this.$router.push({ name: this.routeName });
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForAssignRolesForUsers)) {
                        this.$router.push({ name: this.routeName });
                    }
                }
            }
        },

        mounted() {
            let retrieveRolesPromise = new Promise((resolve, reject) => {
                axios.get('/api/roles/retrieve-all-roles').then(res => {
                    this.roles = res.data.roles;
                    resolve();
                }).catch(err => {
                    console.log(err);
                });
            });

            let retrieveAssignedRoles = new Promise((resolve, reject) => {
                axios.get('/api/users/' + this.$route.params.id + '/assigned-roles').then(res => {
                    this.assignedRoles = res.data.assignedRoles;
                    resolve();
                }).catch(err => {
                    console.log(err);
                });
            });

            let retrieveAdminPromise = new Promise((resolve, reject) => {
                axios.get('/api/users/' + this.$route.params.id).then(res => {
                    this.user = res.data.user;

                    resolve();
                }).catch(err => {
                    console.log(err);
                });
            });

            Promise.all([retrieveRolesPromise, retrieveAssignedRoles, retrieveAdminPromise]).then(() => {

                let roles = this.roles;
                let userRoles = JSON.parse(localStorage.getItem('roles'));

                if (! userRoles.includes('super-administrator')) {
                    roles.forEach((role, index) => {
                        if (role.name === 'super-administrator') {
                            this.roles.splice(index, 1);
                        }
                    });
                }

                this.roles.find(role => {
                    if (_.some(this.assignedRoles, { id: role.id })) {
                        role.isAssigned = true;
                    } else {
                        role.isAssigned = false;
                    }

                    role.requesting = false;
                });

                this.ifReady = true;
            });
        },

        methods: {
            viewUsers() {
                this.$router.push({
                    name: 'users.index'
                });
            },
            isRequesting(index, value) {
                this.roles[index].display_name = this.roles[index].display_name + ' ';
                this.roles[index].requesting = value;
                this.roles[index].display_name.trim();
            },
            toggleRole(index,toggle) {
                this.isRequesting(index, true);

                if (toggle) {
                    document.getElementById('AssignedRole-'+index).classList.add("active");
                    document.getElementById('UnassignedRole-'+index).classList.remove("active");
                } else {
                    document.getElementById('UnassignedRole-'+index).classList.add("active");
                    document.getElementById('AssignedRole-'+index).classList.remove("active");
                }

                let data = {
                    user_id: this.$route.params.id,
                    role_id: this.roles[index].id
                };

                axios.post('/api/users/toggle-role', data).then(res => {
                    this.isRequesting(index, false);
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
