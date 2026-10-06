<template>
    <div>
        <div class="card">
            <div class="card-body">
                <form-title :routePrefixName="routePrefixName" :title="title" v-bind:showRightSide="false"></form-title>

                <br>

                <form-create :apiPath="apiPath" :routePrefixName="routePrefixName" :singularName="singularName" :toastMessage="toastMessage" :fieldColumns="getFieldColumns()">
                    <template v-slot:default v-bind:data="$data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name <small class="text-danger">* Required</small></label>
                                <input id="name" class="form-control" type="text" v-model="name" autocomplete="off" minlength="2" maxlength="255" placeholder="vice-assistant" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="display-name" class="form-label">Display Name <small class="text-danger">* Required</small></label>
                                <input id="display-name" class="form-control" type="text" v-model="display_name" autocomplete="off" minlength="2" maxlength="255" placeholder="Vice Assistant" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Desciption</label>
                                <textarea id="description" class="form-control" v-model="description" rows="3" maxlength="500"></textarea>
                            </div>
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
                title:           'Create New Role',
                singularName:    'Role',
                toastMessage:    'Role',
                pluralName:      'Roles',
                apiPath:         '/api/roles',
                routePrefixName: 'roles',
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
