<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Gross Sales Current Week</h4>
                <h6 class="card-subtitle mb-2 text-body-secondary">{{ startOfWeek }} - {{ endOfWeek }}</h6>
                <br>
                <span class="fs-1 fw-medium">{{ currentWeek }}</span>
                <br>
                <span v-if="isPercentDifferencePositive"><span class="text-success fw-bold"><i class="bi bi-arrow-up"></i> {{ percentDifference }}%</span> Compared Last Week</span>
                <span v-else><span class="text-danger fw-bold"><i class="bi bi-arrow-down"></i> {{ percentDifference }}%</span> Compared Last Week</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                lastWeek: 0,
                currentWeek: 0,
                percentDifference: 0,
                isPercentDifferencePositive: true,
            }
        },

        mounted() {
            let promiseGrossSalesLastMonth = new Promise((resolve, reject) => {
                axios.get('/api/reports/gross-sales/last-week').then(res => {
                    this.lastWeek = res.data.last_week.gross_sales;

                    resolve();
                }).catch(err => {
                    reject();
                });
            });

            let promiseGrossSalesCurrentMonth = new Promise((resolve, reject) => {
                axios.get('/api/reports/gross-sales/current-week').then(res => {
                    this.currentWeek = res.data.current_week.gross_sales;

                    resolve();
                }).catch(err => {
                    reject();
                });
            });

            Promise.all([promiseGrossSalesLastMonth, promiseGrossSalesCurrentMonth]).then(() => {
                let difference = (parseFloat(this.currentWeek) - parseFloat(this.lastWeek));
                let average = (parseFloat(this.currentWeek) + parseFloat(this.lastWeek));
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

                this.currentWeek = this.$moneyFormatter.format(this.currentWeek);

                this.ifReady = true;
            });
        },

        computed: {
            startOfWeek() {
                const now = DateTime.fromISO(DateTime.now());
                return now.startOf('week').toFormat('LLL dd, yyyy (cccc)');
            },
            endOfWeek() {
                const now = DateTime.fromISO(DateTime.now());
                return now.endOf('week').toFormat('LLL dd, yyyy (cccc)');
            }
        }
    }
</script>

