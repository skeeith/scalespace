<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Gross Sales Today</h4>
                <h6 class="card-subtitle mb-2 text-body-secondary">{{ dateToday }}</h6>
                <br>
                <span class="fs-1 fw-medium">{{ today }}</span>
                <br>
                <span v-if="isPercentDifferencePositive"><span class="text-success fw-bold"><i class="bi bi-arrow-up"></i> {{ percentDifference }}%</span> Compared Yesterday</span>
                <span v-else><span class="text-danger fw-bold"><i class="bi bi-arrow-down"></i> {{ percentDifference }}%</span> Compared Yesterday</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                yesterday: 0,
                today: 0,
                percentDifference: 0,
                isPercentDifferencePositive: true,
            }
        },

        mounted() {
            let promiseGrossSalesYesterday = new Promise((resolve, reject) => {
                axios.get('/api/reports/gross-sales/yesterday').then(res => {
                    this.yesterday = res.data.yesterday.gross_sales;

                    resolve();
                }).catch(err => {
                    reject();
                });
            });

            let promiseGrossSalesToday = new Promise((resolve, reject) => {
                axios.get('/api/reports/gross-sales/today').then(res => {
                    this.today = res.data.today.gross_sales;

                    resolve();
                }).catch(err => {
                    reject();
                });
            });

            Promise.all([promiseGrossSalesYesterday, promiseGrossSalesToday]).then(() => {
                let difference = (parseFloat(this.today) - parseFloat(this.yesterday));
                let average = (parseFloat(this.today) + parseFloat(this.yesterday));
                let quotient = (parseFloat(difference) / parseFloat(average));

                let percentDifference = (quotient * 100);

                if (Number.isNaN(percentDifference)) {
                    this.percentDifference = 0;
                } else {
                    this.percentDifference = percentDifference.toFixed(2);
                }

                if (this.percentDifference < 0) {
                    this.isPercentDifferencePositive = false;
                } else {
                    this.isPercentDifferencePositive = true;
                }

                this.today = this.$moneyFormatter.format(this.today);

                this.ifReady = true;
            });
        },

        computed: {
            dateToday() {
                return DateTime.now().toFormat('LLLL dd, yyyy (cccc)');
            }
        }
    }
</script>
