<template>
    <div>
        <div class="card">
            <div class="card-body">
                <form-title :routePrefixName="routePrefixName" :title="title" v-bind:showRightSide="false"></form-title>

                <br>

                <form-view :apiPath="apiPath" :routePrefixName="routePrefixName" :singularName="singularName" :selectedProperty="selectedProperty" :toastMessage="toastMessage" :disableEdit="true" :disableDelete="true">
                    <template v-slot:default v-bind:permission="permission" v-bind:ifReady="ifReady">
                        <div v-if="ifReady" class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Name</label>
                                    <input type="text" class="form-control" v-model="permission.name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Display Name</label>
                                    <input type="text" class="form-control" v-model="permission.display_name">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mb-3">
                                    <label>Desciption</label>
                                    <textarea class="form-control" v-model="permission.description"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </form-view>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                ifReady:          false,
                action:           'View',
                title:            'View Permission',
                toastMessage:     'Permission',
                singularName:     'Permission',
                pluralName:       'Permissions',
                apiPath:          '/api/permissions',
                routePrefixName:  'permissions',
                useName:          'singular',
                selectedProperty: 'display_name',
                object:           {},

                permission:       {}
            };
        },

        mounted() {
            let promise = new Promise((resolve, reject) => {
                axios.get(this.apiPath + '/' + this.$route.params.id).then(res => {
                    this.object = res.data.permission;
                    this.permission = res.data.permission;
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
