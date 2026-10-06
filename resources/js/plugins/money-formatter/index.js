export default {
    install: (app) => {
        app.config.globalProperties.$moneyFormatter = {
            format(data) {
                data = parseFloat(data);

                if (typeof data === 'string' || isNaN(data)) {
                    data = 0;
                }
                
                return data.toLocaleString('en-US', {
                    style: 'currency',
                    currency: 'PHP',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2,
                });
            }
        }
    }
};
