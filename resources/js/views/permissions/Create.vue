<template>
    <div>
        <div class="card">
            <div class="card-body">
                <form-title :routePrefixName="routePrefixName" :title="title" v-bind:showRightSide="false"></form-title>

                <br>

                <form-create :apiPath="apiPath" :routePrefixName="routePrefixName" :singularName="singularName" :toastMessage="toastMessage" :fieldColumns="getFieldColumns()">
                    <template v-slot:default v-bind:data="$data">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" v-model="name" autocomplete="off" minlength="2" maxlength="255" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Display Name</label>
                                <input type="text" class="form-control" v-model="display_name" autocomplete="off" minlength="2" maxlength="255" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="description">Desciption</label>
                            <textarea class="form-control" v-model="description" rows="3" maxlength="500"></textarea>
                        </div>
                    </template>
                </form-create>
            </div>
        </div>
    </div>
</template>

<script>
    import { useLocalStorage } from '@/composables/useLocalStorage.js';

    export default {
        setup() {
            const modulePrefix = 'roles_';

            const name         = useLocalStorage(modulePrefix + 'name', null);
            const display_name = useLocalStorage(modulePrefix + 'display_name', null);
            const description  = useLocalStorage(modulePrefix + 'description', null);

            return {
                modulePrefix,
                name, display_name, description
            };
        },

        data() {
            return {
                ifReady:         true,
                action:          'Create New',
                title:           'Create New Permission',
                singularName:    'Permission',
                toastMessage:    'Permission',
                pluralName:      'Permissions',
                apiPath:         '/api/permissions',
                routePrefixName: 'permissions',
                useName:         'singular',
            };
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
