import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'

export const useCustomerStore = defineStore('customer', {
    state: () => ({

    }),



    actions: {
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("customers/list"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return error.response.data;
                }));
        },
        getProvessioningLog: async function (id) {
            console.log(id);
            return (await AxiosHelper
                .get(AppsbdURL.route("customer/processing-log",id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return error.response.data;
                }));
        },

        getDetail: async function (params) {
           console.log(params);
            return (await AxiosHelper.get(AppsbdURL.route( "customers",params.id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        visitAdminPanel: async function (id) {

            return (await AxiosHelper.get(AppsbdURL.route( "customers/visit-admin",id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        visitNogorPanel: async function (id) {

            return (await AxiosHelper.get(AppsbdURL.route( "customers/visit",id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        approvePayment: async function(params){
            return (await AxiosHelper
                .post(AppsbdURL.route("customers/approve"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {

                    return error.response.data;
                }));
        },
        updateCustomer: async function(params){

            return (await AxiosHelper
                .post(AppsbdURL.route("customers/update"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {

                    return error.response.data;
                }));
        },
        getIsValidDomain: async function(params){

            return (await AxiosHelper
                .post(AppsbdURL.route("customers/check-domain"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error);
                    return error.response.data;
                }));
        },
        rejectPayment: async function(params){
            return (await AxiosHelper
                .post(AppsbdURL.route("temp-customers/reject"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        }
    },
})
