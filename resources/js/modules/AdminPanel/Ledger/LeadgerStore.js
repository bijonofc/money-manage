import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'

export const useLedgerStore = defineStore('ledger', {
    state: () => ({

    }),



    actions: {
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("ledgers/list"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        addPackage: async function (params) {

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
        getPackage: async function (params) {

            return (await AxiosHelper.get(AppsbdURL.route( "packages",params.package_id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
    },
})
