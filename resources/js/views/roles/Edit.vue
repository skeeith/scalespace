<template>
    <div>
        <div class="card">
            <div class="card-body">
                <form-title :routePrefixName="routePrefixName" :title="title" v-bind:showRightSide="false"></form-title>
                <br>
                <form-edit :apiPath="apiPath" :routePrefixName="routePrefixName" :singularName="singularName" :toastMessage="toastMessage" :fieldColumns="getFieldColumns()">
                    <template v-slot:default v-bind:data="$data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name">Name <small class="text-danger">* Required</small></label>
                                <input id="name" type="text" class="form-control" v-model="name" autocomplete="off" minlength="2" maxlength="255" placeholder="vice-assistant" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="display-name">Display Name <small class="text-danger">* Required</small></label>
                                <input id="display-name" type="text" class="form-control" v-model="display_name" autocomplete="off" minlength="2" maxlength="255" placeholder="Vice Assistant" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="description">Desciption</label>
                                <textarea id="description" class="form-control" v-model="description" rows="3" maxlength="500"></textarea>
                            </div>
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
                title:            'Edit Role',
                toastMessage:     'Role',
                singularName:     'Role',
                pluralName:       'Roles',
                apiPath:          '/api/roles',
                routePrefixName:  'roles',
                useName:          'singular',
                selectedProperty: 'display_name',

                name:         null,
                display_name: null,
                description:  null,
            };
        },

        mounted() {
            let promise = new Promise((resolve, reject) => {
                axios.get(this.apiPath + '/' + this.$route.params.id).then(res => {
                    this.name         = res.data.role.name;
                    this.display_name = res.data.role.display_name;
                    this.description  = res.data.role.description;

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
