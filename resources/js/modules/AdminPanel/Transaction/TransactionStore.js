import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'

export const useTransactionStore = defineStore('transaction', {
    state: () => ({

    }),

    actions: {
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("transactions/list"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },

        getDetail: async function (params) {

            return (await AxiosHelper.get(AppsbdURL.route( "transactions",params.id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        getLogs: async function (params) {

            return (await AxiosHelper.get(AppsbdURL.route( "transaction-log",params.id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },


    },
})
