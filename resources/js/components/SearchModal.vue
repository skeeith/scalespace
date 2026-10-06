<template>
    <div class="modal fade" :id="idAttribute" :ref="refAttribute" tabindex="-1" :aria-labelledby="getLabelAttribute()" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" :id="getLabelAttribute()">Search {{ title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <slot name="default"></slot>
                    
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label class="form-label">Order By</label>
                            <select class="form-control" v-model="order_by">
                                <option value="desc">Newest</option>
                                <option value="asc">Oldest</option>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group">
                            <label class="form-label">Is Strict</label>
                            <select class="form-control" v-model="is_strict">
                                <option :value="true">Yes</option>
                                <option :value="false">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer clearfix">
                    <button type="button" class="btn btn-danger btn-sm" @click.prevent="clearParameters()">Clear</button>
                    <button type="button" class="btn btn-success btn-sm" @click.prevent="search()">Search</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            routeName: String,
            idAttribute: String,
            refAttribute: String,
            title: String,
            parameters: null,
            data: Object
        },

        data() {
            return {
                modal: {},
                order_by: 'desc',
                is_strict: false,
            }
        },

        mounted() {
            this.modal = new bootstrap.Modal(`#${this.idAttribute}`, { backdrop: "static ", keyboard: false } );
        },

        methods: {
            search() {
                this.modal.hide();

                this.showProgress = true;

                this.$router.push({
                    name: this.routeName,
                    query: {
                        page: 1,
                        per_page: this.$parent.data.meta.per_page,
                        order_by: this.$route.query.order_by,
                        order_by_field_name: this.$route.query.order_by_field_name,
                        with_trashed: this.$route.query.with_trashed,
                        only_trashed: this.$route.query.only_trashed,
                        is_strict: this.is_strict,
                        ...this.parameters
                    }
                });
            },
            clearParameters() {
                this.$emit('ClearParameters');
            },
            openSearchModal() {
                this.modal.show();
            },
            getLabelAttribute() {
                return `${this.idAttribute}-title`;
            }
        }
    }
</script>
