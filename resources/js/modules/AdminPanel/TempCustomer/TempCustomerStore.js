import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'
import {useLoginStore} from '@/modules/AdminPanel/User/loginStore.js';

export const useTempCustomerStore = defineStore('temp_customer', {
    state: () => ({

    }),



    actions: {
        getLoginStore() {
            return useLoginStore();
        },
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("temp-customers/list"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
       postDetail: async function (params) {

            return (await AxiosHelper.post(AppsbdURL.route( "packages"),params)
                .then(response => {
                    // this.addedProject(response.data.data);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));

        },
        updatePackage: async function (params) {

            return (await AxiosHelper.patch(AppsbdURL.route('packages', params.id),params)
                .then(response => {
                    // this.updatedProject(params.id,response.data.data);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        deletePackage: async function (params) {
            return (await AxiosHelper.delete(AppsbdURL.route( "packages",params.package_id))
                .then(response => {
                    // this.deletedProject(params.project_id);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        getDetail: async function (params) {
           console.log(params);
            return (await AxiosHelper.get(AppsbdURL.route( "temp-customers",params.id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        approvePayment: async function(params){
            return (await AxiosHelper
                .post(AppsbdURL.route("temp-customers/approve"),params)
                .then(response => {
                    this.addedCustomer(response.data.data);
                    return response.data;
                })
                .catch(error => {

                    return error.response.data;
                }));
        },
        updateCustomer: async function(params){

            return (await AxiosHelper
                .post(AppsbdURL.route("temp-customers/update"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {

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
        },
        addedCustomer(customerData)
        {
            this.getLoginStore().customerList.push(customerData);
        },
    },
})
