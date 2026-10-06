<template>
    <div class="clearfix">
        <div v-if="pageCount">
            <nav class="float-start">
                <ul class="pagination">
                    <li class="page-item" v-bind:class="isPrevDisabled">
                        <a class="page-link" href="#" @click.prevent="goToPreviousPage()" disabled><strong>Previous</strong></a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" @click.prevent="goToFirstPage()"><strong>First</strong></a>
                    </li>
                    <li class="page-item" :key="index" v-for="(pageNumber, index) in pageNumbers" v-bind:class="isPageActive(pageNumber)">
                        <a class="page-link" href="#" @click.prevent="goToPage(pageNumber)"><strong>{{ pageNumber }}</strong></a>
                    </li>
                    <li class="page-item" v-bind:class="isNextDisabled">
                        <a class="page-link" href="#" @click.prevent="goToLastPage()"><strong>Last</strong></a>
                    </li>
                    <li class="page-item" v-bind:class="isNextDisabled">
                        <a class="page-link" href="#" @click.prevent="goToNextPage()"><strong>Next</strong></a>
                    </li>
                </ul>
            </nav>
        </div>
        <div v-else>
            <nav class="float-start">
                <ul class="pagination">
                    <li class="page-item" v-bind:class="isPrevDisabled">
                        <a class="page-link" href="#" @click.prevent="goToPreviousPage()" disabled><strong>Previous</strong></a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" @click.prevent="goToFirstPage()"><strong>First</strong></a>
                    </li>
                    <li class="page-item" :key="index" v-for="(pageNumber, index) in pageNumbers" v-bind:class="isPageActive(pageNumber)">
                        <a class="page-link" href="#" @click.prevent="goToPage(pageNumber)"><strong>{{ pageNumber }}</strong></a>
                    </li>
                    <li class="page-item" v-bind:class="isNextDisabled">
                        <a class="page-link" href="#" @click.prevent="goToLastPage()"><strong>Last</strong></a>
                    </li>
                    <li class="page-item" v-bind:class="isNextDisabled">
                        <a class="page-link" href="#" @click.prevent="goToNextPage()"><strong>Next</strong></a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="float-end">
            <div class="btn-toolbar" role="toolbar">
                <slot name="default"></slot>

                <div class="input-group mb-3">
                    <div class="input-group-text" id="btnGroupAddon">{{ capitalizeEachWord(($route.query.order_by_field_name == '' || $route.query.order_by_field_name == null) ? 'id' : $route.query.order_by_field_name) }}</div>
                    <div v-if="($route.query.order_by_field_name == '' || $route.query.order_by_field_name == null || $route.query.order_by_field_name == 'id')" class="btn-group me-2" role="group">
                        <button type="button" class="btn btn-outline-secondary rounded-0" :class="checkSorting('desc')" @click.prevent="sortByDescending()">
                            <strong>Newest</strong>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" :class="checkSorting('asc')" @click.prevent="sortByAscending()">
                            <strong>Oldest</strong>
                        </button>
                    </div>
                    <div v-else class="btn-group me-2" role="group">
                        <button type="button" class="btn btn-outline-secondary rounded-0" :class="checkSorting('desc')" @click.prevent="sortByDescending()">
                            <strong>Decending</strong>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" :class="checkSorting('asc')" @click.prevent="sortByAscending()">
                            <strong>Ascending</strong>
                        </button>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-text" id="btnGroupAddon">Number of Items</div>
                    <select class="form-select" id="number-of-items" aria-describedby="btnGroupAddon" v-model="meta.per_page" v-on:change="changePerPage()">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="75">75</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            routeName: String,
            meta: Object,
            links: Object,
            parameters: null
        },

        data() {
            return {
                pageNumbers: []
            };
        },

        mounted() {
            this.populatePages();
        },

        watch: {
            meta: function(newValue, oldValue) {
                this.populatePages();
            },
            '$route.query.order_by_field_name': function (new_order_by_field_name, old_order_by_field_name) {
                if (new_order_by_field_name === old_order_by_field_name) {
                    return;
                }

                this.$router.push({
                    name: this.routeName,
                    query: {
                        page: this.$route.query.page,
                        per_page: (this.$route.query.per_page != 'undefined') ? this.$route.query.per_page : this.meta.per_page,
                        order_by: this.$route.query.order_by,
                        order_by_field_name: new_order_by_field_name,
                        is_strict: this.$route.query.is_strict,
                        ...this.parameters
                    }
                });
            }
        },

        computed: {
            nextPage() {
                return this.meta.current_page + 1;
            },
            prevPage() {
                return this.meta.current_page - 1;
            },
            paginatonCount() {
                if (! this.meta) {
                    return;
                }

                const { current_page, last_page } = this.meta;

                return `${current_page} of ${last_page}`;
            },
            pageCount() {
                if (this.meta.last_page > 10) {
                    return false;
                }

                return true;
            },
            isPrevDisabled() {
                if (this.links.prev == null) {
                    return 'disabled';
                }

                return;
            },
            isNextDisabled() {
                if (this.links.next == null) {
                    return 'disabled';
                }

                return;
            }
        },

        methods: {
            goToFirstPage() {
                this.$router.push({
                    name: this.routeName,
                    query: {
                        page: 1,
                        per_page: this.meta.per_page,
                        order_by: this.$route.query.order_by,
                        order_by_field_name: this.$route.query.order_by_field_name,
                        with_trashed: this.$route.query.with_trashed,
                        is_strict: this.$route.query.is_strict,
                        only_trashed: this.$route.query.only_trashed,
                        ...this.parameters
                    }
                });
            },
            goToPage(page = null) {
                this.$router.push({
                    name: this.routeName,
                    query: {
                        page,
                        per_page: this.meta.per_page,
                        order_by: this.$route.query.order_by,
                        order_by_field_name: this.$route.query.order_by_field_name,
                        with_trashed: this.$route.query.with_trashed,
                        only_trashed: this.$route.query.only_trashed,
                        is_strict: this.$route.query.is_strict,
                        ...this.parameters
                    },
                });
            },
            goToLastPage() {
                this.$router.push({
                    name: this.routeName,
                    query: {
                        page: this.meta.last_page,
                        per_page: this.meta.per_page,
                        order_by: this.$route.query.order_by,
                        order_by_field_name: this.$route.query.order_by_field_name,
                        with_trashed: this.$route.query.with_trashed,
                        only_trashed: this.$route.query.only_trashed,
                        is_strict: this.$route.query.is_strict,
                        ...this.parameters
                    }
                });
            },
            goToNextPage() {
                this.$router.push({
                    name: this.routeName,
                    query: {
                        page: this.nextPage,
                        per_page: this.meta.per_page,
                        order_by: this.$route.query.order_by,
                        order_by_field_name: this.$route.query.order_by_field_name,
                        with_trashed: this.$route.query.with_trashed,
                        only_trashed: this.$route.query.only_trashed,
                        is_strict: this.$route.query.is_strict,
                        ...this.parameters
                    }
                });
            },
            goToPreviousPage() {
                this.$router.push({
                    name: this.routeName,
                    query: {
                        page: this.prevPage,
                        per_page: this.meta.per_page,
                        order_by: this.$route.query.order_by,
                        order_by_field_name: this.$route.query.order_by_field_name,
                        with_trashed: this.$route.query.with_trashed,
                        only_trashed: this.$route.query.only_trashed,
                        is_strict: this.$route.query.is_strict,
                        ...this.parameters
                    }
                });
            },
            populatePages() {
                this.pageNumbers = [];

                if (this.pageCount) {
                    for (let i = 1; i <= this.meta.last_page; i++) {
                        this.pageNumbers.push(i);
                    }
                } else if (this.meta.current_page <= 6) {
                    let page = 1;
                    this.pageNumbers.push(page);
                    this.pageNumbers.push(page + 1);
                    this.pageNumbers.push(page + 2);
                    this.pageNumbers.push(page + 3);
                    this.pageNumbers.push(page + 4);
                    this.pageNumbers.push(page + 5);
                    this.pageNumbers.push(page + 6);
                    this.pageNumbers.push('...');
                    this.pageNumbers.push(this.meta.last_page - 1);
                    this.pageNumbers.push(this.meta.last_page);
                } else if ((this.meta.current_page + 6) >= this.meta.last_page) {
                    this.pageNumbers.push(1);
                    this.pageNumbers.push(2);
                    this.pageNumbers.push('...');
                    this.pageNumbers.push(this.meta.last_page - 7);
                    this.pageNumbers.push(this.meta.last_page - 6);
                    this.pageNumbers.push(this.meta.last_page - 5);
                    this.pageNumbers.push(this.meta.last_page - 4);
                    this.pageNumbers.push(this.meta.last_page - 3);
                    this.pageNumbers.push(this.meta.last_page - 2);
                    this.pageNumbers.push(this.meta.last_page - 1);
                    this.pageNumbers.push(this.meta.last_page);
                } else {
                    this.pageNumbers.push(1);
                    this.pageNumbers.push(2);
                    this.pageNumbers.push('...');
                    this.pageNumbers.push(this.meta.current_page - 2);
                    this.pageNumbers.push(this.meta.current_page - 1);
                    this.pageNumbers.push(this.meta.current_page);
                    this.pageNumbers.push(this.meta.current_page + 1);
                    this.pageNumbers.push(this.meta.current_page + 2);
                    this.pageNumbers.push('...');
                    this.pageNumbers.push(this.meta.last_page - 1);
                    this.pageNumbers.push(this.meta.last_page);
                }
            },
            isPageActive(page) {
                if (page == this.$route.query.page || (page == 1 && this.$route.query.page == null)) {
                    return 'active';
                }

                return;
            },
            changePerPage() {
                this.$router.push({
                    name: this.routeName,
                    query: {
                        page: 1,
                        per_page: this.meta.per_page,
                        order_by: this.$route.query.order_by,
                        order_by_field_name: this.$route.query.order_by_field_name,
                        with_trashed: this.$route.query.with_trashed,
                        only_trashed: this.$route.query.only_trashed,
                        is_strict: this.$route.query.is_strict,
                        ...this.parameters
                    }
                });
            },
            checkSorting(sortingValue) {
                if (
                    (typeof this.$route.query.order_by === 'undefined' || this.$route.query.order_by === null) &&
                    sortingValue !== 'asc'
                ) {
                    return { active: true };
                }

                if (this.$route.query.order_by === sortingValue) {
                    return { active: true };
                }

                return { active: false };
            },
            sortByAscending() {
                this.$router.push({
                    name: this.routeName,
                    query: {
                        page: this.$route.query.page,
                        per_page: this.meta.per_page,
                        order_by: 'asc',
                        order_by_field_name: (this.$route.query.order_by === 'asc') ? 'id' : this.$route.query.order_by_field_name,
                        with_trashed: this.$route.query.with_trashed,
                        only_trashed: this.$route.query.only_trashed,
                        is_strict: this.$route.query.is_strict,
                        ...this.parameters
                    }
                });
            },
            sortByDescending() {
                this.$router.push({
                    name: this.routeName,
                    query: {
                        page: this.$route.query.page,
                        per_page: this.meta.per_page,
                        order_by: 'desc',
                        order_by_field_name: (this.$route.query.order_by === 'desc') ? 'id' : this.$route.query.order_by_field_name,
                        with_trashed: this.$route.query.with_trashed,
                        only_trashed: this.$route.query.only_trashed,
                        is_strict: this.$route.query.is_strict,
                        ...this.parameters
                    }
                });
            },
            capitalizeEachWord(string) {
                string = string.replaceAll('_', ' ');

                return string.toLowerCase().split(' ').map(word => {
                    return word.charAt(0).toUpperCase() + word.slice(1);
                }).join(' ');
            }
        }
    }
</script>
