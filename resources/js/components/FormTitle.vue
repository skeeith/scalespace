<template>
    <div class="clearfix">
        <div class="float-start">
            <h2>{{ title }} <slot name="title-small"></slot></h2>
        </div>

        <div class="float-end">
            <slot name="float-end">
                <div class="btn-toolbar" role="toolbar">

                    <slot name="other-buttons-before-quick-search"></slot>

                    <button v-if="showRightSide == true && showClearSearchButton" type="button" class="btn btn-info btn-sm me-2" @click.prevent="clearSearch()">
                        <i class="fas fa-eraser"></i>
                        &nbsp;
                        <strong>Clear Search</strong>
                    </button>

                    <div v-if="showRightSide == true" class="input-group input-group-sm me-2">
                        <div class="input-group-text" id="btnGroupAddon">
                            <i class="fa-brands fa-searchengin"></i>
                        </div>
                        <input type="text" class="form-control" v-model="searchAllField" @keyup="onSearch()" placeholder="Quick Search" autocomplete="off">
                    </div>

                    <slot name="other-buttons-before-trash"></slot>

                    <div class="btn-group" role="group" aria-label="With Trash and Only Trashed">
                        <button v-if="showRightSide == true" type="button" class="btn btn-outline-danger btn-sm" :class="{ active: $route.query.with_trashed == 1}" @click.prevent="searchWithTrashed($route.query.with_trashed)">
                            <i class="fa-solid fa-trash-can-arrow-up"></i>
                            &nbsp;
                            <strong>With Trashed</strong>
                        </button>
                        <button v-if="showRightSide == true" type="button" class="btn btn-outline-danger btn-sm me-2" :class="{ active: $route.query.only_trashed == 1 }" @click.prevent="searchOnlyTrashed($route.query.only_trashed)">
                            <i class="fa-solid fa-trash-can"></i>
                            &nbsp;
                            <strong>Only Trashed</strong>
                        </button>
                    </div>

                    <slot name="other-buttons-before-create-new"></slot>

                    <router-link v-if="showRightSide == true && showCreateNew && showCreatePermission" class="btn btn-success btn-sm me-2" :to="{ name: `${routePrefixName}.create` }">
                        <i class="fas fa-plus"></i>
                        &nbsp;
                        <strong>Create New {{ singularName }}</strong>
                    </router-link>

                    <slot name="other-buttons-before-advance-search"></slot>

                    <button v-if="showRightSide == true && data" type="button" class="btn btn-primary btn-sm me-2" @click.prevent="$parent.$refs.searchModal.openSearchModal()">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        &nbsp;
                        <strong>Advance Search {{ pluralName }}</strong>
                    </button>
                    
                    <slot name="other-buttons"></slot>
                </div>
            </slot>
        </div>
    </div>
</template>

<script>
    import _ from 'lodash';

    export default {
        props: {
            routePrefixName: {
                type: String,
                required: false
            },
            title: {
                type: String,
                required: true
            },
            singularName: {
                type: String,
                required: false
            },
            pluralName: {
                type: String,
                required: false
            },
            showRightSide: {
                type: Boolean,
                required: false
            },
            showCreateNew: {
                type: Boolean,
                required: false,
                default: true
            },
            data: {
                type: Object,
                required: false
            },
            parameters: {
                type: Object,
                required: false
            },
            additionalIgnoreFields: {
                type: Array,
                required: false,
                default: []
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
        },

        data() {
            return {
                showClearSearchButton: false,
                searchAllField: null,
                ignoreFields: ['order_by', 'order_by_field_name','page','per_page', 'with_trashed', 'only_trashed', 'is_strict'],
                userPermissions: [],
                showCreatePermission: true,
                requiredPermissionsForCreate: ['full-access-and-permssions', 'create-' + this.routePrefixName],
            };
        },

        created() {
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
            this.ignoreFields = this.ignoreFields.concat(this.additionalIgnoreFields);

            for (const [key, value] of Object.entries(this.$route.query)) {
                if (! this.ignoreFields.includes(key)) {
                    this.searchAllField = this.$route.query[key];
                }
            }

            if (this.checkForPermissions) {
                // For Create Permission
                if (Array.isArray(this.requiredPermissionsForCreate)) {
                    if (! this.requiredPermissionsForCreate.some(requiredPermissionForCreate => this.userPermissions.includes(requiredPermissionForCreate) === true)) {
                        this.showCreatePermission = false;
                    }
                } else {
                    if (! this.userPermissions.includes(requiredPermissionsForCreate)) {
                        this.showCreatePermission = false;
                    }
                }
            }
        },

        watch: {
            '$route.query': function (new_query, old_query) {
                this.showClearSearchButton = false;

                let queryParams = {};

                for (const [key, value] of Object.entries(this.$route.query)) {
                    if (typeof value !== 'null' || typeof value !== 'object' || typeof value !== null) {
                        queryParams[key] = value;
                    }
                }

                for (const key of this.ignoreFields) {
                    delete queryParams[key];
                }

                const parameters = Object.entries(queryParams);

                for (const [key, value] of Object.entries(parameters)) {
                    if (value[1] !== "") {
                        this.showClearSearchButton = true;
                    }
                }
            }
        },

        methods: {
            clearSearch() {
                this.$parent.$refs.searchModal.clearParameters();

                this.searchAllField = '';

                this.$parent.$refs.searchModal.search();
            },
            searchWithTrashed(value) {
                for (const [key, value] of Object.entries(this.parameters)) {
                    if (! this.ignoreFields.includes(key)) {
                        this.parameters[key] = this.$data.searchAllField;
                    }
                }

                this.$parent.showProgress = true;
                this.parameters.with_trashed = value == 1 ? 0 : 1;
                this.parameters.only_trashed = 0;

                this.$router.push({
                    name: this.$parent.routeName,
                    query: {
                        page: 1,
                        per_page: this.$parent.data.meta.per_page,
                        order_by: this.$route.query.order_by,
                        order_by_field_name: this.$route.query.order_by_field_name,
                        is_strict: this.$route.query.is_strict,
                        ...this.parameters
                    }
                });
            },
            searchOnlyTrashed(value) {
                for (const [key, value] of Object.entries(this.parameters)) {
                    if (! this.ignoreFields.includes(key)) {
                        this.parameters[key] = this.$data.searchAllField;
                    }
                }

                this.$parent.showProgress = true;
                this.parameters.with_trashed = 0;
                this.parameters.only_trashed = value == 1 ? 0 : 1;

                this.$router.push({
                    name: this.$parent.routeName,
                    query: {
                        page: 1,
                        per_page: this.$parent.data.meta.per_page,
                        order_by: this.$route.query.order_by,
                        order_by_field_name: this.$route.query.order_by_field_name,
                        is_strict: this.$route.query.is_strict,
                        ...this.parameters
                    }
                });
            },
            onSearch() {
                this.search(this.parameters, this);
            },
            search: _.debounce((parameters, vm) => {
                for (const [key, value] of Object.entries(parameters)) {
                    if (! vm.ignoreFields.includes(key)) {
                        parameters[key] = vm.$data.searchAllField;
                    }
                }

                vm.$router.push({
                    name: vm.$parent.$parent.routeName,
                    query: {
                        page: 1,
                        per_page: vm.$parent.data.meta.per_page,
                        order_by: vm.$route.query.order_by,
                        order_by_field_name: vm.$route.query.order_by_field_name,
                        with_trashed: vm.$route.query.with_trashed,
                        only_trashed: vm.$route.query.only_trashed,
                        is_strict: vm.$route.query.is_strict,
                        ...parameters
                    }
                });
            }, 350)
        }
    }
</script>
