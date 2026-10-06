<template>
    <th scope="col" :width="width" @click="sortByFieldName()">
        <span v-if="! isLoading">
            <span v-show="showDefaultSorting"><i class="fa-solid fa-sort"></i></span>
            <span v-show="showAscendingSorting"><i class="fa-solid fa-sort-up"></i></span>
            <span v-show="showDescendingSorting"><i class="fa-solid fa-sort-down"></i></span>&nbsp;
        </span>

        <slot name="default"></slot>
    </th>
</template>

<script>
    export default {
        props: {
            width:{
                type: Number,
                required: false,
                default: 0
            },
            fieldName: {
                type: String,
                required: false,
                default: ''
            },
            isAction: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        data() {
            return {
                isLoading: false,
                order_by_field_name: 'id',
                order_by: null,
                showDefaultSorting: true,
                showAscendingSorting: false,
                showDescendingSorting: false,
            }
        },

        mounted() {
            this.order_by = this.$route.query.order_by;
            this.order_by_field_name = this.$route.query.order_by_field_name;

            this.checkSortingForDefault();
            this.checkSortingForAscending();
            this.checkSortingForDescending();

            this.$forceUpdate();
        },

        watch: {
            '$route.query.order_by': function (new_order_by, old_order_by) {
                if (new_order_by === old_order_by) {
                    return;
                }

                this.order_by = new_order_by;

                this.checkSortingForDefault();
                this.checkSortingForAscending();
                this.checkSortingForDescending();

                this.$forceUpdate();
            },
            '$route.query': function (new_order_by, old_order_by) {
                this.checkSortingForDefault();
                this.checkSortingForAscending();
                this.checkSortingForDescending();

                this.$forceUpdate();
            },
        },

        methods: {
            async sortByFieldName() {
                if (this.isAction) {
                    return;
                }

                if (this.$route.query.order_by === 'asc' && this.$route.query.order_by_field_name === this.fieldName) {
                    this.order_by_field_name = 'id';
                    this.order_by = null;
                } else if (
                    (typeof this.order_by === 'object' && typeof this.$route.query.order_by_field_name === 'undefined' && typeof this.$route.query.order_by === 'undefined') ||
                    (typeof this.order_by === 'object' && typeof this.$route.query.order_by_field_name === 'string' && typeof this.$route.query.order_by === 'string') ||
                    (typeof this.order_by === 'object' && typeof this.$route.query.order_by_field_name === 'string' && typeof this.$route.query.order_by === 'object') ||
                    (
                        typeof this.order_by === 'string' &&
                        this.order_by === 'desc' &&
                        typeof this.$route.query.order_by === 'string' &&
                        this.$route.query.order_by === 'desc' &&
                        typeof this.$route.query.order_by_field_name === 'string' &&
                        this.$route.query.order_by_field_name !== this.fieldName
                    ) ||
                    (
                        typeof this.order_by === 'string' &&
                        this.order_by === 'asc' &&
                        typeof this.$route.query.order_by === 'string' &&
                        this.$route.query.order_by === 'asc' &&
                        typeof this.$route.query.order_by_field_name === 'string' &&
                        this.$route.query.order_by_field_name !== this.fieldName
                    ) ||
                    (
                        typeof this.order_by === 'undefined' &&
                        typeof this.$route.query.order_by === 'undefined' &&
                        typeof this.$route.query.order_by_field_name === 'undefined' &&
                        this.$route.query.order_by_field_name !== this.fieldName
                    )
                ) {
                    this.order_by_field_name = this.fieldName;
                    this.order_by = 'desc';
                } else if (
                    (
                        typeof this.order_by === 'string' &&
                        this.order_by === 'desc' &&
                        typeof this.$route.query.order_by_field_name === 'string' &&
                        this.$route.query.order_by_field_name === this.fieldName
                    ) ||
                    (
                        typeof this.order_by === 'object' &&
                        this.order_by === null &&
                        typeof this.$route.query.order_by === 'string' &&
                        this.$route.query.order_by === 'desc' &&
                        typeof this.$route.query.order_by_field_name === 'string' &&
                        this.$route.query.order_by_field_name === this.fieldName
                    )
                ) {
                    this.order_by_field_name = this.fieldName;
                    this.order_by = 'asc';
                }

                this.isLoading = true;

                const navigationResult = await this.$router.push({
                    name: this.$parent.$parent.routeName,
                    query: {
                        page: this.$route.query.page,
                        per_page: this.$parent.$parent.data.meta.per_page,
                        order_by: this.order_by,
                        order_by_field_name: this.order_by_field_name,
                        with_trashed: this.$route.query.with_trashed,
                        only_trashed: this.$route.query.only_trashed,
                        ...this.$parent.$parent.fieldNames,
                    }
                });

                if (navigationResult) {
                    console.log('Navigation prevented or aborted');
                }

                this.isLoading = false;
            },
            checkSortingForDefault() {
                if (
                    this.$route.query.order_by_field_name !== this.fieldName &&
                    (this.$route.query.order_by !== 'desc' || this.$route.query.order_by !== 'asc')
                ) {
                    this.showDefaultSorting = true;
                } else {
                    this.showDefaultSorting = false;
                }
            },
            checkSortingForAscending() {
                if (
                    (this.order_by === 'asc' && this.$route.query.order_by_field_name === this.fieldName && ! this.isAction) ||
                    (this.$route.query.order_by === 'asc' && this.$route.query.order_by_field_name === this.fieldName && ! this.isAction)
                ) {
                    this.showAscendingSorting = true;
                } else {
                    this.showAscendingSorting = false;
                }
            },
            checkSortingForDescending() {
                if (
                    (this.order_by === 'desc' && this.$route.query.order_by_field_name === this.fieldName && ! this.isAction) ||
                    (this.$route.query.order_by === 'desc' && this.$route.query.order_by_field_name === this.fieldName && ! this.isAction)
                ) {
                    this.showDescendingSorting = true;
                } else {
                    this.showDescendingSorting = false;
                }
            }
        }
    }
</script>
