<template>
    <div>
        <div class="card">
            <div class="card-body">
                <form-title :data="data" :routePrefixName="routePrefixName" :title="title" :singularName="singularName" :pluralName="pluralName" v-bind:showRightSide="true" :parameters="getParameters()"></form-title>

                <br>

                <data-table :data="data" :pluralName="pluralName" :showProgress="showProgress">
                    <template v-slot:thead>
                        <tr>
                            <data-table-header :fieldName="'name_from_userable_from'">From</data-table-header>
                            <data-table-header :fieldName="'name'">Name</data-table-header>
                            <data-table-header :fieldName="'email'">Email</data-table-header>
                            <data-table-header :fieldName="'mobile_number'">Mobile Number</data-table-header>
                            <data-table-header :isAction="true">Actions</data-table-header>
                        </tr>
                    </template>
                    <template v-slot:tbody v-bind:data="data">
                        <tr :id="user.id" :key="user.id" v-for="user in data?.users">
                            <td>{{ user.userable_from_type }} {{ user.userable_from?.name }}</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.mobile_number }}</td>
                            <td>
                                <data-table-row-action :routePrefixName="routePrefixName" :object="user">
                                    <template v-slot:default v-bind:user="$user">
                                        <span v-if="showAssignRolesForUsersPermission"><strong>&nbsp; | &nbsp;</strong></span>
                                        <router-link v-if="showAssignRolesForUsersPermission" class="text-secondary" :to="{ name: 'users.assign-roles', params: { id: user.id } }">
                                             <i class="fa-solid fa-person-military-to-person"></i>
                                             &nbsp;
                                             <strong>Assign Roles</strong>
                                        </router-link>
                                    </template>
                                </data-table-row-action>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>

        <br>

        <pagination v-if="data" :routeName="routeName" :meta="data.meta" :links="data.links" :parameters="getParameters()"></pagination>

        <search-modal v-if="data" :data="data" :routeName="routeName" idAttribute="search-modal" ref="searchModal" :title="singularName" :parameters="getParameters()" @ClearParameters="clearParameters()">
            <template v-slot:default>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name-from-branch-or-warehouse" class="form-label">Name From Branch or Warehouse</label>
                        <input id="name-from-branch-or-warehouse" class="form-control" type="text" v-model="fieldNames.name_from_userable_from" maxlength="255" autocomplete="off">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" class="form-control" type="email" v-model="fieldNames.email" maxlength="255" autocomplete="off">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" class="form-control" type="text" v-model="fieldNames.name" maxlength="255" autocomplete="off">
                    </div>
                </div>
            </template>
        </search-modal>
    </div>
</template>

<script>
    import indexMixins from '@/mixins/Index';

    export default {
        mixins: [indexMixins],

        data() {
            return {
                data:            null,
                action:          'View',
                singularName:    'User',
                pluralName:      'Users',
                title:           'Users',
                apiPath:         '/api/users',
                routeName:       'users.index',
                routePrefixName: 'users',
                dataAttribute:   'users',
                showProgress:    false,

                userPermissions: [],
                showAssignRolesForUsersPermission: true,
                requiredPermissionsForAssignRolesForUsers: ['full-access-and-permssions', 'assign-roles-users'],
                checkForCustomPermissions: true,

                fieldNames: {
                    name_from_userable_from: null,
                    email: null,
                    name: null,
                }
            };
        },

        beforeRouteEnter(to, from) {
            if (to.query.per_page == null) {
                return (vm) => {
                    vm.getData({
                        page:                to.query.page,
                        per_page:            15,
                        order_by:            to.query.order_by,
                        order_by_field_name: to.query.order_by_field_name,
                        only_trashed:        to.query.only_trashed,
                        with_trashed:        to.query.with_trashed,
                        is_strict:           to.query.is_strict,
                        ...vm.$data.fieldNames
                    }, vm.$data.dataAttribute);
                };
            } else {
                return (vm) => {
                    vm.getData({
                        page:                to.query.page,
                        per_page:            to.query.per_page,
                        order_by:            to.query.order_by,
                        order_by_field_name: to.query.order_by_field_name,
                        only_trashed:        to.query.only_trashed,
                        with_trashed:        to.query.with_trashed,
                        is_strict:           to.query.is_strict,
                        ...vm.$data.fieldNames
                    }, vm.$data.dataAttribute);
                };
            }
        },

        beforeRouteUpdate(to, from) {
            this.getData({
                page:                to.query.page,
                per_page:            this.data.meta.per_page,
                order_by:            to.query.order_by,
                order_by_field_name: to.query.order_by_field_name,
                only_trashed:        to.query.only_trashed,
                with_trashed:        to.query.with_trashed,
                is_strict:           to.query.is_strict,
                ...this.fieldNames
            }, this.$data.dataAttribute);
        },

        watch: {
            data(new_data, old_data) {
                let authenticatedUser = JSON.parse(localStorage.getItem('user'));

                let users = this.data.users;

                users.forEach((user, index) => {
                    if (user.name === 'Super Administrator' && parseInt(authenticatedUser.id) !== 1) {
                        new_data.users.splice(index, 1);
                    }
                });

                let userableFrom = {
                    'App\\Models\\Branch': "Branch",
                    'App\\Models\\Warehouse': "Warehouse"
                };

                this.data.users.map(user => {
                    if (typeof user.userable_from_type === 'string' && typeof user.userable_from_id === 'number') {
                        user.userable_from_type = userableFrom[user.userable_from_type];
                    }
                });
            }
        },

        created() {
            Broadcast.$on('User-Retrieved', (event) => {
                this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
            });

            if (localStorage.getItem('permissions') !== null) {
                this.userPermissions = JSON.parse(localStorage.getItem('permissions'));
            }

            if (this.checkForCustomPermissions) {
                // For Create Permission
                if (Array.isArray(this.requiredPermissionsForAssignRolesForUsers)) {
                    if (! this.requiredPermissionsForAssignRolesForUsers.some(requiredPermissionForAssignRolesForUsers => this.userPermissions.includes(requiredPermissionForAssignRolesForUsers) === true)) {
                        this.showAssignRolesForUsersPermission = false;
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForAssignRolesForUsers)) {
                        this.showAssignRolesForUsersPermission = false;
                    }
                }
            }
        },

    }
</script>
