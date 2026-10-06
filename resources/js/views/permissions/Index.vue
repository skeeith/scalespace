<template>
    <div>
        <div class="card">
            <div class="card-body">
                <form-title :data="data" :routePrefixName="routePrefixName" :title="title" :singularName="singularName" :pluralName="pluralName" v-bind:showRightSide="true" :parameters="getParameters()" :showCreateNew="false"></form-title>

                <br>

                <data-table :data="data" :pluralName="pluralName" :showProgress="showProgress">
                    <template v-slot:thead>
                        <tr>
                            <data-table-header :fieldName="'name'">Name</data-table-header>
                            <data-table-header :fieldName="'display_name'">Display Name</data-table-header>
                            <data-table-header :fieldName="'module'">Module</data-table-header>
                            <data-table-header :isAction="true">Actions</data-table-header>
                        </tr>
                    </template>
                    <template v-slot:tbody v-bind:data="data">
                        <tr :id="permission.id" :key="permission.id" v-for="permission in data?.permissions">
                            <td>{{ permission.name }}</td>
                            <td>{{ permission.display_name }}</td>
                            <td>{{ permission.module }}</td>
                            <td><data-table-row-action :routePrefixName="routePrefixName" :object="permission" :disableEdit="true"></data-table-row-action></td>
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
                singularName:    'Permission',
                pluralName:      'Permissions',
                title:           'Permissions',
                apiPath:         '/api/permissions',
                routeName:       'permissions.index',
                routePrefixName: 'permissions',
                routePrefixName: 'permissions',
                dataAttribute:   'permissions',
                showProgress:    false,

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
    }
</script>
