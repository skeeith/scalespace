<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Gross Sales Current Week Per Day</h4>
                <h6 class="card-subtitle mb-2 text-body-secondary">{{ startOfWeek }} - {{ endOfWeek }}</h6>
                <br>
                <canvas ref="grossSalesCurrentWeekPerDay"></canvas>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                labels: {
                    1: 'Monday',
                    2: 'Tuesday',
                    3: 'Wednesday',
                    4: 'Thursday',
                    5: 'Friday',
                    6: 'Saturday',
                    7: 'Sunday',
                },
                currentWeekPerDayValues: {
                    1: 0,
                    2: 0,
                    3: 0,
                    4: 0,
                    5: 0,
                    6: 0,
                    7: 0,
                },
            }
        },

        mounted() {
            let promise = new Promise((resolve, reject) => {
                axios.get('/api/reports/gross-sales/current-week-per-day').then(res => {
                    let currentWeekPerDay = res.data.current_week_per_day;

                    for (const weekDayNumber in this.currentWeekPerDayValues) {
                        currentWeekPerDay.forEach((perDayReport) => {
                            if (this.currentWeekPerDayValues.hasOwnProperty(weekDayNumber) && perDayReport.number_of_week_day === parseInt(weekDayNumber)) {
                                this.currentWeekPerDayValues[weekDayNumber] = perDayReport.gross_sales;
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
                const ctx = this.$refs.grossSalesCurrentWeekPerDay;

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.values(this.labels),
                        datasets: [{
                            label: 'Gross Sales Current Week Per Day',
                            data: Object.values(this.currentWeekPerDayValues),
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
