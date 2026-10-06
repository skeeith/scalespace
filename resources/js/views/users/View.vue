<template>
    <div class="card">
        <div class="card-body">
            <form-title :routePrefixName="routePrefixName" :title="title" v-bind:showRightSide="false"></form-title>

            <br>

            <form-view :apiPath="apiPath" :routePrefixName="routePrefixName" :singularName="singularName" :selectedProperty="selectedProperty" :toastMessage="toastMessage">
                <template v-slot:default v-bind:data="$data">
                    <fieldset>
                        <legend>Location Information</legend>

                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <label for="userable-from-name" class="form-label">From</label>
                                <input id="userable-from-name" class="form-control" type="text" v-model="userable_from.name" disabled>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="userable-from-city" class="form-label">City</label>
                                <input id="userable-from-city" class="form-control" type="text" v-model="userable_from.city" disabled>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="userable-from-telephone-number" class="form-label">Telephone Number</label>
                                <input id="userable-from-telephone-number" class="form-control" type="text" v-model="userable_from.telephone_number" disabled>
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
            </form-view>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                ifReady:          false,
                action:           'View',
                title:            'View User',
                toastMessage:     'User',
                singularName:     'User',
                pluralName:       'Users',
                apiPath:          '/api/users',
                routePrefixName:  'users',
                useName:          'singular',
                selectedProperty: 'display_name',
                object:           {},

                from_selected_radio_button: 'branch',

                userable_from: null,
                name:          null,
                email:         null,

                mobile_number: null,
                telephone_number: null,

                address:  null,
                city:     null,
                province: null,
                zip_code: null,
            };
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
                    this.object        = res.data.user;
                    this.userable_from = res.data.user.userable_from;
                    this.name          = res.data.user.name;
                    this.email         = res.data.user.email;

                    this.mobile_number = res.data.user.mobile_number;
                    this.telephone_number = res.data.user.telephone_number;

                    this.address = res.data.user.address;
                    this.city = res.data.user.city;
                    this.province = res.data.user.province;
                    this.zip_code = res.data.user.zip_code;

                    if (this.userable_from_type === 'App\\Models\\Branch') {
                        this.from_selected_radio_button = 'branch';
                        this.userable_from.type = 'Branch';
                    } else if (this.userable_from_type === 'App\\Models\\Warehouse') {
                        this.from_selected_radio_button = 'warehouse';
                        this.userable_from.type = 'Warehosue';
                    } else {
                        this.from_selected_radio_button = '';
                    }

                    resolve();
                }).catch(err => {
                    reject();
                });
            });

            promise.then(() => {
                this.ifReady = true;
            });
        }
    }
</script>
