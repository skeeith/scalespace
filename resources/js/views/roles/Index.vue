<template>
    <div>
        <div class="card">
            <div class="card-body">
                <form-title :data="data" :routePrefixName="routePrefixName" :title="title" :singularName="singularName" :pluralName="pluralName" v-bind:showRightSide="true" :parameters="getParameters()"></form-title>

                <br>

                <data-table :data="data" :pluralName="pluralName" :showProgress="showProgress">
                    <template v-slot:thead>
                        <tr>
                            <data-table-header :fieldName="'name'">Name</data-table-header>
                            <data-table-header :fieldName="'display_name'">Display Name</data-table-header>
                            <data-table-header :isAction="true">Actions</data-table-header>
                        </tr>
                    </template>
                    <template v-slot:tbody v-bind:data="data">
                        <tr :id="role.id" :key="role.id" v-for="role in data?.roles">
                            <td>{{ role.name }}</td>
                            <td>{{ role.display_name }}</td>
                            <td>
                                <data-table-row-action :routePrefixName="routePrefixName" :object="role">
                                    <template v-slot:default v-bind:role="$role">
                                        <span v-if="showAssignPermissionsForRolesPermission"><strong>&nbsp; | &nbsp;</strong></span>
                                        <router-link v-if="showAssignPermissionsForRolesPermission" class="text-secondary" :to="{ name: 'roles.assign-permissions', params: { id: role.id } }">
                                            <i class="fa-solid fa-person-military-to-person"></i>
                                            &nbsp;
                                            <strong>Assign Permissions</strong>
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
                        <label for="name" class="form-label">Name</label>
                        <input id="name" class="form-control" type="text" v-model="fieldNames.name" maxlength="255" autocomplete="off">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="display-name" class="form-label">Display Name</label>
                        <input id="display-name" class="form-control" type="text" v-model="fieldNames.display_name" maxlength="255" autocomplete="off">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="description" class="form-label">Descrption</label>
                        <input id="description" class="form-control" type="text" v-model="fieldNames.description" maxlength="255" autocomplete="off">
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
                singularName:    'Role',
                pluralName:      'Roles',
                title:           'Roles',
                apiPath:         '/api/roles',
                routeName:       'roles.index',
                routePrefixName: 'roles',
                dataAttribute:   'roles',
                showProgress:    false,

                userPermissions: [],
                showAssignPermissionsForRolesPermission: true,
                requiredPermissionsForAssignPermissionsForRolesPermission: ['full-access-and-permssions', 'assign-permissions-for-roles'],
                checkForCustomPermissions: true,

                fieldNames: {
                    name:         null,
                    display_name: null,
                    description:  null,
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
                let roles = this.data.roles;
                let userRoles = JSON.parse(localStorage.getItem('roles'));

                if (! userRoles.includes('super-administrator')) {
                    roles.forEach((role, index) => {
                        if (role.name === 'super-administrator') {
                            this.data.roles.splice(index, 1);
                        }
                    });
                }
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
                if (Array.isArray(this.requiredPermissionsForAssignPermissionsForRolesPermission)) {
                    if (! this.requiredPermissionsForAssignPermissionsForRolesPermission.some(requiredPermissionForAssignPermissionsForRolesPermission => this.userPermissions.includes(requiredPermissionForAssignPermissionsForRolesPermission) === true)) {
                        this.showAssignPermissionsForRolesPermission = false;
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForAssignPermissionsForRolesPermission)) {
                        this.showAssignPermissionsForRolesPermission = false;
                    }
                }
            }
        }
    }
</script>
