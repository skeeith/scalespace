<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Gross Sales Last Month</h4>
                <h6 class="card-subtitle mb-2 text-body-secondary">{{ dateOfTheMonth }}</h6>
                <br>
                <span class="fs-1 fw-medium">{{ lastMonth }}</span>
                <br>
                <span>&nbsp;</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                lastMonth: 0
            }
        },

        mounted() {
            let promise = new Promise((resolve, reject) => {
                axios.get('/api/reports/gross-sales/last-month').then(res => {
                    this.lastMonth = this.$moneyFormatter.format(res.data.last_month.gross_sales);

                    resolve();
                }).catch(err => {
                    reject();
                });
            });

            promise.then(() => {
                this.ifReady = true;
            });
        },

        computed: {
            dateOfTheMonth() {
                return DateTime.now().plus({ months: -1 }).toFormat('LLLL  yyyy');
            }
        }
    }
</script>
