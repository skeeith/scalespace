<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ graphLabel }}</h4>
                <h6 class="card-subtitle mb-2 text-body-secondary">{{ startOfYear }} - {{ endOfYear }}</h6>
                <br>
                <canvas ref="monthlyEngineOilGrossSales"></canvas>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                graphLabel: 'Monthly Car Engine Oil Gross Sales',
                labels: {
                    1: 'January',
                    2: 'Febuary',
                    3: 'March',
                    4: 'April',
                    5: 'May',
                    6: 'June',
                    7: 'July',
                    8: 'August',
                    9: 'September',
                    10: 'October',
                    11: 'November',
                    12: 'December',
                },
                monthlyValues: {
                    1: 0,
                    2: 0,
                    3: 0,
                    4: 0,
                    5: 0,
                    6: 0,
                    7: 0,
                    8: 0,
                    9: 0,
                    10: 0,
                    11: 0,
                    12: 0,
                },
            }
        },

        mounted() {
            let promise = new Promise((resolve, reject) => {
                axios.get('/api/reports/custom-reports/monthly-engine-oil-gross-sales').then(res => {
                    let monthlyEngineOilGrossSales = res.data.monthly_engine_oil_gross_sales;

                    for (const monthNumber in this.monthlyValues) {
                        monthlyEngineOilGrossSales.forEach((perMonthEngineOilVolumeSales) => {
                            if (this.monthlyValues.hasOwnProperty(monthNumber) && perMonthEngineOilVolumeSales.month_number === parseInt(monthNumber)) {
                                this.monthlyValues[monthNumber] = perMonthEngineOilVolumeSales.grand_total_amount;
                            }
                        });
                    }

                    resolve();
                }).catch(err => {
                    console.log('err', err);
                    reject();
                });
            });

            promise.then(() => {
                const ctx = this.$refs.monthlyEngineOilGrossSales;

                ctx.height = 75;

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.values(this.labels),
                        datasets: [{
                            label: this.graphLabel,
                            data: Object.values(this.monthlyValues),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                this.ifReady = true;
            });
        },

        computed: {
            startOfYear() {
                const now = DateTime.fromISO(DateTime.now());
                return now.startOf('year').toFormat('LLLL  yyyy');
            },
            endOfYear() {
                const now = DateTime.fromISO(DateTime.now());
                return now.endOf('year').toFormat('LLLL  yyyy');
            }
        }
    }
</script>
