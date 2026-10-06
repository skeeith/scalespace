<template>
    <div class="card">
        <div class="card-body">
            <form-title :routePrefixName="routePrefixName" :title="title" v-bind:showRightSide="false"></form-title>

            <br>

            <form-create :apiPath="apiPath" :routePrefixName="routePrefixName" :singularName="singularName" :toastMessage="toastMessage" :fieldColumns="getFieldColumns()">
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
                                <input-search v-if="from_selected_radio_button === 'warehouse'" :id="'from'" :apiPath="'/api/warehouses/search'" :placeholder="'Search Warehouses'" :fieldToDisplay="'name'" :data-selector="'warehouses'" :objectToUpdate="'warehouse'" :selectedObject="'warehouse'" :fieldToUpdate="'warehouse_id'" :objectToDisplay="warehouse" :required="false"></input-search>
                                <input-search v-if="from_selected_radio_button === 'branch'" :id="'from'" :apiPath="'/api/branches/search'" :placeholder="'Search Branches'" :fieldToDisplay="'name'" :data-selector="'branches'" :objectToUpdate="'branch'" :selectedObject="'branch'" :fieldToUpdate="'branch_id'" :objectToDisplay="branch" :required="false"></input-search>
                            </div>
                            <div v-if="from_selected_radio_button !== 'all'" class="col-lg-4 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input v-if="from_selected_radio_button === 'branch'" id="city" type="text" class="form-control" v-model="branch.city" disabled>
                                <input v-if="from_selected_radio_button === 'warehouse'" id="city" type="text" class="form-control" v-model="warehouse.city" disabled>
                            </div>
                            <div v-if="from_selected_radio_button !== 'all'" class="col-lg-4 mb-3">
                                <label for="telephone-number" class="form-label">Telephone Number</label>
                                <input v-if="from_selected_radio_button === 'branch'" id="branch-telephone-number" type="text" class="form-control" v-model="branch.telephone_number" disabled>
                                <input v-if="from_selected_radio_button === 'warehouse'" id="branch-telephone-number" type="text" class="form-control" v-model="warehouse.telephone_number" disabled>
                            </div>
                        </div>
                    </fieldset>
                    
                    <br>

                    <fieldset>
                        <legend>User Credential Information</legend>

                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="name" class="form-label">Name <small class="text-danger">* Required</small></label>
                                <input id="name" class="form-control" type="text" v-model="name" autocomplete="off" minlength="2" maxlength="255" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="email" class="form-label">Email Address <small class="text-danger">* Required</small></label>
                                <input id="email" class="form-control" type="email" v-model="email" autocomplete="off" minlength="2" maxlength="255" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="password" class="form-label">Password <small class="text-danger">* Required</small></label>
                                <input id="password" class="form-control" type="password" v-model="password" autocomplete="off" minlength="2" maxlength="255" required>
                                <!-- <p v-if="$v.password_confirmation.$error">Password Does Not Match Password Confirmation</p> -->
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="password-confirmation" class="form-label">Password Confirmation</label>
                                <input id="password-confirmation" class="form-control" type="password" v-model="password_confirmation" @blur="$v.password_confirmation.$touch()">
                                <!-- <p v-if="$v.password_confirmation.$error">Password Confirmation Does Not Match Password</p> -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="security-pin" class="form-label">Security Pin <small class="text-danger">* Required</small></label>
                                <input id="security-pin" class="form-control" type="password" v-model="security_pin" autocomplete="off" minlength="2" maxlength="255" required>
                                <!-- <p v-if="$v.password_confirmation.$error">Password Does Not Match Password Confirmation</p> -->
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="security-pin-confirmation" class="form-label">Security Pin Confirmation</label>
                                <input id="security-pin-confirmation" class="form-control" type="password" v-model="security_pin_confirmation" @blur="$v.security_pin_confirmation.$touch()">
                                <!-- <p v-if="$v.password_confirmation.$error">Password Confirmation Does Not Match Password</p> -->
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
            </form-create>
        </div>
    </div>
</template>

<script>
    import { useLocalStorage } from '@/composables/useLocalStorage.js';

    export default {
        setup() {
            const modulePrefix = 'users_create_';

            const branch                     = useLocalStorage(modulePrefix + 'branch', {});
            const warehouse                  = useLocalStorage(modulePrefix + 'warehouse', {});
            const from_selected_radio_button = useLocalStorage(modulePrefix + 'from_selected_radio_button', 'branch');

            const userable_from_type        = useLocalStorage(modulePrefix + 'userable_from_type', null);
            const userable_from_id          = useLocalStorage(modulePrefix + 'userable_from_id', null);
            const name                      = useLocalStorage(modulePrefix + 'name', null);
            const email                     = useLocalStorage(modulePrefix + 'email', null);
            const password                  = useLocalStorage(modulePrefix + 'password', null);
            const password_confirmation     = useLocalStorage(modulePrefix + 'password_confirmation', null);
            const security_pin              = useLocalStorage(modulePrefix + 'security_pin', null);
            const security_pin_confirmation = useLocalStorage(modulePrefix + 'security_pin_confirmation', null);

            const mobile_number     = useLocalStorage(modulePrefix + 'mobile_number', null);
            const telephone_number = useLocalStorage(modulePrefix + 'telephone_number', null);

            const address  = useLocalStorage(modulePrefix + 'address', null);
            const city     = useLocalStorage(modulePrefix + 'city', null);
            const province = useLocalStorage(modulePrefix + 'province', null);
            const zip_code = useLocalStorage(modulePrefix + 'zip_code', null);

            return {
                modulePrefix,
                branch, warehouse, from_selected_radio_button,
                userable_from_type, userable_from_id,
                name, email,
                password, password_confirmation,
                security_pin, security_pin_confirmation,
                mobile_number, telephone_number,
                address, city, province, zip_code
            };
        },

        data() {
            return {
                ifReady:         true,
                action:          'Create New',
                title:           'Create New User',
                singularName:    'User',
                toastMessage:    'User',
                pluralName:      'Users',
                apiPath:         '/api/users',
                routePrefixName: 'users',
                useName:         'singular',
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
            if (localStorage.getItem('userableFrom') !== null) {
                this.userPermissions = JSON.parse(localStorage.getItem('userableFrom'));
            }
        },

        methods: {
            getFieldColumns() {
                if (this.from_selected_radio_button === 'all') {
                    this.userable_from_type = null;
                    this.userable_from_id = null;
                }
                
                return {
                    userable_from_type:        this.userable_from_type,
                    userable_from_id:          this.userable_from_id,
                    name:                      this.name,
                    email:                     this.email,
                    password:                  this.password,
                    password_confirmation:     this.password_confirmation,
                    security_pin:              this.security_pin,
                    security_pin_confirmation: this.security_pin_confirmation,

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
