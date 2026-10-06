<template>
    <div class="card">
        <div class="card-body">
            <form-title :routePrefixName="routePrefixName" :title="title" v-bind:showRightSide="false"></form-title>

            <br>

            <form-edit :apiPath="apiPath" :routePrefixName="routePrefixName" :singularName="singularName" :toastMessage="toastMessage" :fieldColumns="getFieldColumns()">
                <template v-slot:default v-bind:data="$data">
                    <fieldset>
                        <legend>Location Information</legend>

                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <label from="from" class="form-label">From</label>
                                &nbsp;
                                <div class="form-check form-check-inline">
                                    <input id="from-warehouse" class="form-check-input" type="radio" v-model="from_selected_radio_button" value="warehouse">
                                    <label for="from-warehouse" class="form-check-label">&nbsp; Warehouse</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input id="from-branch" class="form-check-input" type="radio" v-model="from_selected_radio_button" value="branch">
                                    <label for="from-branch" class="form-check-label">&nbsp; Branch</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input id="from-all" class="form-check-input" type="radio" v-model="from_selected_radio_button" value="all">
                                    <label for="from-all" class="form-check-label">&nbsp; All Location</label>
                                </div>
                                <input-search v-if="from_selected_radio_button === 'warehouse'" :id="'from'" :apiPath="'/api/warehouses/search'" :placeholder="'Search Warehouses'" :fieldToDisplay="'name'" :data-selector="'warehouses'" :objectToUpdate="'warehouse'" :selectedObject="'warehouse'" :fieldToUpdate="'warehouse_id'" :objectToDisplay="warehouse" ></input-search>
                                <input-search v-if="from_selected_radio_button === 'branch'" :id="'from'" :apiPath="'/api/branches/search'" :placeholder="'Search Branches'" :fieldToDisplay="'name'" :data-selector="'branches'" :objectToUpdate="'branch'" :selectedObject="'branch'" :fieldToUpdate="'branch_id'" :objectToDisplay="branch"></input-search>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input v-if="from_selected_radio_button === 'branch'" id="city" type="text" class="form-control" v-model="branch.city" disabled>
                                <input v-if="from_selected_radio_button === 'warehouse'" id="city" type="text" class="form-control" v-model="warehouse.city" disabled>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="telephone-number" class="form-label">Telephone Number</label>
                                <input v-if="from_selected_radio_button === 'branch'" id="branch-telephone-number" type="text" class="form-control" v-model="branch.telephone_number" disabled>
                                <input v-if="from_selected_radio_button === 'warehouse'" id="branch-telephone-number" type="text" class="form-control" v-model="warehouse.telephone_number" disabled>
                            </div>
                        </div>
                    </fieldset>
                    
                    <br>

                    <fieldset>
                        <legend>User Credentials Information</legend>

                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="name" class="form-label">Name <small class="text-danger">* Required</small></label>
                                <input id="name" class="form-control" type="text" v-model="name" autocomplete="off" minlength="2" maxlength="255" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="email" class="form-label">Email Address <small class="text-danger">* Required</small></label>
                                <input id="email" class="form-control" type="email" v-model="email" autocomplete="off" minlength="2" maxlength="255" disabled>
                            </div>
                        </div>
                    </fieldset>

                    <br>

                    <fieldset>
                        <legend>User Information</legend>
                        <div class="row">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label for="phone-number" class="form-label">Mobile Number</label>
                                <input id="phone-number" class="form-control" type="text" v-model="mobile_number" autocomplete="off" minlength="10" maxlength="15">
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label for="telephone-number" class="form-label">Telephone Number</label>
                                <input id="telephone-number" class="form-control" type="text" v-model="telephone_number" autocomplete="off" minlength="8" maxlength="14">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea id="address" class="form-control" v-model="address" rows="2" autocomplete="off"></textarea>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input id="city" class="form-control" type="text" v-model="city" autocomplete="off" minlength="2" maxlength="255">
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label for="province" class="form-label">Province</label>
                                <input id="province" class="form-control" type="text" v-model="province" autocomplete="off" minlength="2" maxlength="255">
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label for="zip-code" class="form-label">Zip Code</label>
                                <input id="zip-code" class="form-control" type="text" v-model="zip_code" autocomplete="off" minlength="3" maxlength="7">
                            </div>
                        </div>
                    </fieldset>
                </template>
            </form-edit>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                ifReady:          false,
                action:           'Edit',
                title:            'Edit User',
                toastMessage:     'User',
                singularName:     'User',
                pluralName:       'Users',
                apiPath:          '/api/users',
                routePrefixName:  'users',
                useName:          'singular',

                branch: {},
                warehouse: {},
                from_selected_radio_button: 'branch',

                userable_from_type: null,
                userable_from_id:   null,
                name:               null,
                email:              null,

                mobile_number: null,
                telephone_number: null,

                address:  null,
                city:     null,
                province: null,
                zip_code: null,
            };
        },

        watch: {
            from_selected_radio_button(new_from_selected_radio_button, old_from_selected_radio_button) {
                this.branch = {};
                this.warehouse = {};

                if (new_from_selected_radio_button === 'all') {
                    this.userable_from_type = null;
                    this.userable_from_id = null;
                }
            },
            branch(new_branch, old_branch) {
                this.userable_from_type = "App\\Models\\Branch";
                this.userable_from_id = new_branch.id;
            },
            warehouse(new_warehouse, old_warehouse) {
                this.userable_from_type = "App\\Models\\Warehouse";
                this.userable_from_id = new_warehouse.id;
            },
        },

        created() {
            let userRoles = JSON.parse(localStorage.getItem('roles'));

            if ((parseInt(this.$route.params.id) === 1 || this.$route.params.id == 1) && ! userRoles.includes('super-administrator')) {
                this.$router.push({ name: `users.index` });
            }
        },

        mounted() {
            let promise = new Promise((resolve, reject) => {
                axios.get(this.apiPath + '/' + this.$route.params.id).then(res => {
                    this.userable_from      = res.data.user.userable_from;
                    this.userable_from_type = res.data.user.userable_from_type;
                    this.userable_from_id   = res.data.user.userable_from_id;

                    this.name = res.data.user.name;
                    this.email = res.data.user.email;

                    this.mobile_number = res.data.user.mobile_number;
                    this.telephone_number = res.data.user.telephone_number;

                    this.address = res.data.user.address;
                    this.city = res.data.user.city;
                    this.province = res.data.user.province;
                    this.zip_code = res.data.user.zip_code;

                    if (this.userable_from_type === 'App\\Models\\Branch') {
                        this.from_selected_radio_button = 'branch';
                        this.branch = res.data.user.userable_from;
                    } else if (this.userable_from_type === 'App\\Models\\Warehouse') {
                        this.from_selected_radio_button = 'warehouse';
                        this.warehouse = res.data.user.userable_from;
                    } else {
                        this.from_selected_radio_button = 'all';
                    }

                    resolve();
                }).catch(err => {
                    console.log('err', err);
                    reject();
                });
            });

            promise.then(() => {
                this.ifReady = true;
            });
        },

        methods: {
            getFieldColumns() {
                if (this.from_selected_radio_button === 'all') {
                    this.userable_from_type = null;
                    this.userable_from_id = null;
                }

                return {
                    userable_from_type: this.userable_from_type,
                    userable_from_id: this.userable_from_id,

                    name: this.name,

                    mobile_number: this.mobile_number,
                    telephone_number: this.telephone_number,

                    address:  this.address,
                    city:     this.city,
                    province: this.province,
                    zip_code: this.zip_code
                };
            }
        }
    }
</script>
