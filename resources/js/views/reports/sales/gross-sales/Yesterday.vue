<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Gross Sales Yesterday</h4>
                <h6 class="card-subtitle mb-2 text-body-secondary">{{ dateYesterday }}</h6>
                <br>
                <span class="fs-1 fw-medium">{{ yesterday }}</span>
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
                yesterday: 0
            }
        },

        mounted() {
            let promise = new Promise((resolve, reject) => {
                axios.get('/api/reports/gross-sales/yesterday').then(res => {
                    this.yesterday = this.$moneyFormatter.format(res.data.yesterday.gross_sales);

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
            dateYesterday() {
                return DateTime.now().plus({ days: -1 }).toFormat('LLLL dd, yyyy (cccc)');
            }
        }
    }
</script>
