<template>
    <div>
        <div class="card">
            <div class="card-body">
                <form-title :routePrefixName="routePrefixName" :title="title" v-bind:showRightSide="false"></form-title>

                <br>

                <form-edit :apiPath="apiPath" :routePrefixName="routePrefixName" :singularName="singularName" :toastMessage="toastMessage" :fieldColumns="getFieldColumns()">
                    <template v-slot:default v-bind:data="$data">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" v-model="name" autocomplete="off" minlength="2" maxlength="255" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Display Name</label>
                                <input type="text" class="form-control" v-model="display_name" autocomplete="off" minlength="2" maxlength="255">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="description">Desciption</label>
                            <textarea class="form-control" v-model="description" rows="3" maxlength="500"></textarea>
                        </div>
                    </template>
                </form-edit>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                ifReady:          false,
                action:           'Edit',
                title:            'Edit Permission',
                toastMessage:     'Permission',
                singularName:     'Permission',
                pluralName:       'Permissions',
                apiPath:          '/api/permissions',
                routePrefixName:  'permissions',
                useName:          'singular',
                selectedProperty: 'display_name',
                name:             null,
                display_name:     null,
                description:      null
            };
        },

        mounted() {
            let promise = new Promise((resolve, reject) => {
                axios.get(this.apiPath + '/' + this.$route.params.id).then(res => {
                    this.name         = res.data.permission.name;
                    this.display_name = res.data.permission.display_name;
                    this.description  = res.data.permission.description;

                    resolve();
                }).catch(err => {
                    reject();
                });
            });

            promise.then(() => {
                this.ifReady = true;
            });
        },

        methods: {
            getFieldColumns() {
                return {
                    name:         this.name,
                    display_name: this.display_name,
                    description:  this.description
                };
            }
        }
    }
</script>
