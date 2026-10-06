<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Gross Sales Last Week</h4>
                <h6 class="card-subtitle mb-2 text-body-secondary">{{ startOfWeek }} - {{ endOfWeek }}</h6>
                <br>
                <span class="fs-1 fw-medium">{{ lastWeek }}</span>
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
                lastWeek: 0
            }
        },

        mounted() {
            let promise = new Promise((resolve, reject) => {
                axios.get('/api/reports/gross-sales/last-week').then(res => {
                    this.lastWeek = this.$moneyFormatter.format(res.data.last_week.gross_sales);

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
            startOfWeek() {
                const now = DateTime.fromISO(DateTime.now());
                return now.startOf('week', { useLocaleWeeks: true }).plus({ days: -7 }).toFormat('LLL dd, yyyy (cccc)');
            },
            endOfWeek() {
                const now = DateTime.fromISO(DateTime.now());
                return now.endOf('week', { useLocaleWeeks: true }).plus({ days: -7 }).toFormat('LLL dd, yyyy (cccc)');
            }
        }
    }
</script>
