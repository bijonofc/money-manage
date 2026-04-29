import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'

export const useCompanyStore = defineStore('company', {
    state: () => ({
        companyData: {},
    }),



    actions: {
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("company-ledgers/list"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        getDetails: async function (params) {
            return (await AxiosHelper
                .get(AppsbdURL.route("company-ledgers",params.ledger_id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        getCompanyData: async function (params) {
            return (await AxiosHelper
                .get(AppsbdURL.route("company-data",1))
                .then(response => {
                    return response.data;
                })
                .catch(error => {

                    return error.response.data;
                }));
        },
        addExpense: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("company-ledgers/add-expense"),params,true)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        addIncome: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("company-ledgers/add-income"),params,true)
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
