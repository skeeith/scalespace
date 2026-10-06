<template>
    <h1>Net Sales</h1>
    <canvas ref="netSalesYesterday"></canvas>
</template>

<script>
    export default {
        data() {
            return {
                //
            }
        },

        mounted() {
            // const ctx = document.getElementById('myChart');
            const ctx = this.$refs.netSalesYesterday;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    datasets: [{
                        label: '# of Votes',
                        data: [12, 19, 3, 5, 2, 3],
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
        }
    }
</script>
