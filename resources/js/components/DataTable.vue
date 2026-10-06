<template>
    <div class="table-responsive">
        <table class="table table-hover caption-top text-nowrap">
            <thead v-if="data">
                <slot name="thead"></slot>
            </thead>

            <tbody v-if="data" class="table-group-divider">
                <slot name="tbody"></slot>
            </tbody>

            <caption>
                <div class="row">
                    <div class="col-md-9">
                        List of {{ pluralName }} - <strong>Total {{ data?.meta?.total }}</strong>
                    </div>
                    <div class="col-md-3">
                        <div class="progress" v-if="showProgress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </caption>
        </table>
    </div>
</template>

<script>
    export default {
        props: {
            data: {
                type: [Object, Array],
                required: false,
                default: []
            },
            objectSelector: {
                type: String,
                required: false,
                default: 'data'
            },
            hideProgress: {
                type: Boolean,
                required: false,
                default: false
            },
            pluralName: {
                type: String,
                required: false,
                default: ''
            },
            total: {
                type: [String, Number],
                required: false,
                default: '0'
            },
            showProgress: {
                type: Boolean,
                required: false,
                default: false
            },
            fieldNames: {
                type: Object,
                required: false
            }
        },

        data() {
            return {
                isDataTableReady: false,
                localData: this.data,
            }
        },

        mounted() {
            // console.log('this.data', this.localData);
            // console.log('this.pluralName', this.pluralName);
        },

        watch: {
            data(newData, oldData) {
                if (newData.length > 0) {
                    this.isDataTableReady = true;
                } else {
                    this.isDataTableReady = false;
                }
            }
        }
    }
</script>

<style>
    table th:last-child,
    table td:last-child {
        position: sticky;
        right: 0;
        z-index: 2;
    }
</style>
