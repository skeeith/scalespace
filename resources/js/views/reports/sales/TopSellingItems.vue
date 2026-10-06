<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title pb-3">TOP SELLING ITEMS</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Item Name</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Brand 1 All Terrain Tires</td>
                            <td>5842.00</td>
                            <td>15</td>
                            <td>87,630.00</td>
                        </tr>
                        <tr>
                            <td>Brand 1 Off Road Tires</td>
                            <td>4730.00</td>
                            <td>13</td>
                            <td>61,490.00</td>
                        </tr>
                        <tr>
                            <td>Brand 1 Road Tires</td>
                            <td>9478.00</td>
                            <td>10</td>
                            <td>94,780.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<style>
    tr:last-child {
        border-bottom: transparent;
    }
</style>

<script>
    export default {
        data() {
            return {
                reportValue: 0
            }
        },

        mounted() {
            let promise = new Promise((resolve, reject) => {
                axios.get('/api/reports/gross-sales/today').then(res => {
                    this.reportValue = res.data.today;

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
            dateToday() {
                return DateTime.now().toFormat('LLL dd, yyyy (cccc)');
            }
        }
    }
</script>
