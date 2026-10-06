import axios from 'axios';
import { DateTime } from "luxon";

export default {
    install: (app) => {
        app.config.globalProperties.$generate = {
            referenceNumber(documentName, parameters) {
                let total = 0;
                let documentEvent = '';

                async function documentReferenceNumber() {
                    let promiseGetReferenceNumberTotalCount = new Promise((resolve, reject) => {

                        let data = {
                            document_name: documentName,
                            contact_id: parameters.contact_id
                        };

                        axios.post('/api/documents/get-reference-number-total-count/', data).then(res => {
                            total = res.data.total;
                            resolve();
                        }).catch(err => {
                            console.log(err);
                            reject();
                        });
                    });

                    return promiseGetReferenceNumberTotalCount.then(() => {
                        const dateTimeNow = DateTime.now();

                        const referenceNumber = "RN-" + pad() + pad(parameters.contact_id + 1, 8) + "-" + dateTimeNow.toFormat('yyyy') + dateTimeNow.toFormat('MM') + dateTimeNow.toFormat('dd') +  "-" + pad(total + 1, 4);

                        switch(documentName) {
                            case 'purchase-order':
                                documentEvent = 'NewPurchaseOrderReferenceNumberGenerated';
                                break;

                            case 'quotation':
                                documentEvent = 'NewQuotationReferenceNumberGenerated';
                                break;

                            case 'invoice':
                                documentEvent = 'NewInvoiceReferenceNumberGenerated';
                                break;

                            case 'document-memorandum':
                                documentEvent = 'NewDocumentMemoDocumentNumberGenerated';
                                break;
                        }

                        Broadcast.$emit(documentEvent, {
                            referenceNumber: referenceNumber
                        });
                    });
                }

                return documentReferenceNumber();
            },

            number(documentName, options) {
                let place = '';
                let id = '';
                let total = 0;
                let documentEvent = '';
                let prefix = '';

                async function waitForDocumentNumber() {
                    let promiseGetNumberTotalCount = new Promise((resolve, reject) => {
                        let data = {
                            document_name: documentName,
                            branch_id: options.branch_id,
                            warehouse_id: options.warehouse_id
                        };

                        axios.post('/api/documents/get-number-total-count/', data).then(res => {
                            total = res.data.total;
                            resolve();
                        }).catch(err => {
                            console.log(err);
                            reject();
                        });
                    });

                    if ('branch_id' in options) {
                        id = options.branch_id;
                        place = 'B';
                    } else {
                        id = options.warehouse_id;
                        place = 'W';
                    }

                    return promiseGetNumberTotalCount.then(() => {
                        const dateTimeNow = DateTime.now();
                        const number = dateTimeNow.toFormat('yyyy') + dateTimeNow.toFormat('MM') + dateTimeNow.toFormat('dd') + "-" + place + pad(id, 5) + "-" + pad(total + 1, 8);

                        switch(documentName) {
                            case 'purchase-order':
                                documentEvent = 'NewPurchaseOrderNumberGenerated';
                                prefix = 'PO';

                                break;

                            case 'receive-order':
                                documentEvent = 'NewReceiveOrderNumberGenerated';
                                prefix = 'RO';

                                break;

                            case 'stock-request':
                                documentEvent = 'NewStockRequestNumberGenerated';
                                prefix = 'SR';

                                break;

                            case 'bill':
                                documentEvent = 'NewBillNumberGenerated';
                                prefix = 'B';

                                break;

                            case 'bill-payment':
                                documentEvent = 'NewBillPaymentNumberGenerated';
                                prefix = 'BP';

                                break;

                            case 'quotation':
                                documentEvent = 'NewQuotationNumberGenerated';
                                prefix = 'Q';

                                break;

                            case 'invoice':
                                documentEvent = 'NewInvoiceNumberGenerated';
                                prefix = 'I';

                                break;

                            case 'invoice-payment':
                                documentEvent = 'NewInvoicePaymentNumberGenerated';
                                prefix = 'IP';

                                break;

                            case 'credit-memorandum':
                                documentEvent = 'NewCreditMemorandumNumberGenerated';
                                prefix = 'CM';

                                break;

                            case 'debit-memorandum':
                                documentEvent = 'NewDebitMemorandumNumberGenerated';
                                prefix = 'DM';

                                break;
                        }

                        Broadcast.$emit(documentEvent, {
                            number: prefix + '-' + number
                        });
                    });
                }

                return waitForDocumentNumber();
            }
        };
    },
};

function pad(number, width, z) {
    z = z || '0';
    number = number + '';
    return number.length >= width ? number : new Array(width - number.length + 1).join(z) + number;
}
