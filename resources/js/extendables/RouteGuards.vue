<script>
    export default {
        beforeRouteEnter(to, from, next) {
            console.log('beforeRouteEnter');
            if (to.query.per_page == null) {
                next(vm => vm.getData({
                    page:         to.query.page,
                    per_page:     to.query.per_page,
                    order_by:     to.query.order_by,
                    only_trashed: to.query.only_trashed,
                    with_trashed: to.query.with_trashed,
                    ...vm.$data.fieldNames
                }, vm.$data.dataAttribute));
            } else {
                next(vm => vm.getData({
                    page:         to.query.page,
                    per_page:     to.query.per_page,
                    order_by:     to.query.order_by,
                    only_trashed: to.query.only_trashed,
                    with_trashed: to.query.with_trashed,
                    ...vm.$data.fieldNames
                }, vm.$data.dataAttribute));
            }
        },

        beforeRouteUpdate(to, from, next) {
            this.getData({
                page:         to.query.page,
                per_page:     this.data.meta.per_page,
                order_by:     to.query.order_by,
                only_trashed: to.query.only_trashed,
                with_trashed: to.query.with_trashed,
                ...this.fieldNames
            }, vm.$data.dataAttribute);

            next();
        }
    }
</script>
