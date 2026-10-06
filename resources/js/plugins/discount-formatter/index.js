export default {
    install: (app) => {
        app.config.globalProperties.$discountFormatter = {
            toArray(data) {
                if (data == null || data.length === 0) {
                    return;
                }
                
                if (Array.isArray(data) && ! data[0].hasOwnProperty('discount')) {
                    data.map(item => {
                        let purchaseDiscounts = [];
                        let salesDiscounts = [];

                        if (item.purchase_discounts !== null) {
                            item.purchase_discounts = Object(item.purchase_discounts);
                            item.purchase_discounts.forEach(item => {
                                purchaseDiscounts.push(parseInt(item.discount_percent));
                            });

                            item.purchase_discounts = purchaseDiscounts;
                        }

                        if (item.sale_discounts !== null) {
                            item.sale_discounts = Object(item.sale_discounts);
                            item.sale_discounts.forEach(item => {
                                salesDiscounts.push(parseInt(item.discount_percent));
                            });

                            item.sale_discounts = salesDiscounts;
                        }
                    });

                    return data;
                } else {
                    let discounts = [];

                    if (data !== null) {
                        data = Object(data);
                        data.forEach(item => {
                            discounts.push(item.discount_percent);
                        });
                    }

                    return discounts;
                }
            },
            
            toViewable(data) {
                if (data == null || data.length === 0) {
                    return;
                }

                if (Array.isArray(data) && ! data[0].hasOwnProperty('discount')) {
                    data.map(item => {
                        let purchaseDiscounts = '';
                        let salesDiscounts = '';

                        if (item.purchase_discounts !== null) {
                            item.purchase_discounts = Object(item.purchase_discounts);
                            item.purchase_discounts.forEach((item, index, array) => {
                                purchaseDiscounts += item.quantity + ':' + item.discount_percent + '%';

                                if (! Object.is(array.length - 1, index)) {
                                    purchaseDiscounts += ', ';
                                }
                            });

                            item.purchase_discounts = purchaseDiscounts;
                        }

                        if (item.sale_discounts !== null) {
                            item.sale_discounts = Object(item.sale_discounts);
                            item.sale_discounts.forEach((item, index, array) => {
                                salesDiscounts += item.quantity + ':' + item.discount_percent + '%';

                                if (! Object.is(array.length - 1, index)) {
                                    salesDiscounts += `, `;
                                }
                            });

                            item.sale_discounts = salesDiscounts;
                        }
                    });

                    return data;
                } else {
                    let discounts = "";

                    data.forEach((item, index, array) => {
                        discounts += item.discount_percent + '%';

                        if (! Object.is(array.length - 1, index)) {
                            discounts += ' ';
                        }
                    });

                    return discounts;
                }
            }
        }
    }
};
