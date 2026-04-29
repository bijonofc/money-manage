import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'

export const useInvoiceStore = defineStore('invoice', {
    state: () => ({

    }),

    actions: {
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("invoices/list"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        genInvoice: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("invoices/gen"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },

        getDetail: async function (params) {

            return (await AxiosHelper.get(AppsbdURL.route( "invoices",params.id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
       getInvoice: async function (params) {
            return (await AxiosHelper.get(AppsbdURL.route( "invoice",params.invoice_id))
                .then(response => {
                    if (response.status)
                    {
                        return response.data;
                    }})
                .catch(error => {
                    console.log(error);

                    return  error.response.data;
                }));

        },

    },
})
