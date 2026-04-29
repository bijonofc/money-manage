import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'

export const usePaymentStore = defineStore('payment', {
    state: () => ({

    }),



    actions: {
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("payments/list"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
    },
})
