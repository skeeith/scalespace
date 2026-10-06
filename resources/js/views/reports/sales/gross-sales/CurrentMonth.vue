<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Gross Sales Current Month</h4>
                <h6 class="card-subtitle mb-2 text-body-secondary">{{ dateOfTheMonth }}</h6>
                <br>
                <span class="fs-1 fw-medium">{{ currentMonth }}</span>
                <br>
                <span v-if="isPercentDifferencePositive"><span class="text-success fw-bold"><i class="bi bi-arrow-up"></i> {{ percentDifference }}%</span> Compared Last Month</span>
                <span v-else><span class="text-danger fw-bold"><i class="bi bi-arrow-down"></i> {{ percentDifference }}%</span> Compared Last Month</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                lastMonth: 0,
                currentMonth: 0,
                percentDifference: 0,
                isPercentDifferencePositive: true,
            }
        },

        mounted() {
            let promiseGrossSalesLastMonth = new Promise((resolve, reject) => {
                axios.get('/api/reports/gross-sales/last-month').then(res => {
                    this.lastMonth = res.data.last_month.gross_sales;

                    resolve();
                }).catch(err => {
                    reject();
                });
            });

            let promiseGrossSalesCurrentMonth = new Promise((resolve, reject) => {
                axios.get('/api/reports/gross-sales/current-month').then(res => {
                    this.currentMonth = res.data.current_month.gross_sales;

                    resolve();
                }).catch(err => {
                    reject();
                });
            });

            Promise.all([promiseGrossSalesLastMonth, promiseGrossSalesCurrentMonth]).then(() => {
                let difference = (parseFloat(this.currentMonth) - parseFloat(this.lastMonth));
                let average = (parseFloat(this.currentMonth) + parseFloat(this.lastMonth));
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

                this.currentMonth = this.$moneyFormatter.format(this.currentMonth);

                this.ifReady = true;
            });
        },

        computed: {
            dateOfTheMonth() {
                return DateTime.now().toFormat('LLLL  yyyy');
            }
        }
    }
</script>
