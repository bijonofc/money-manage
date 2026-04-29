import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'
import {useLoginStore} from '@/modules/AdminPanel/User/loginStore.js';

export const usePackageStore = defineStore('package', {
    state: () => ({

    }),



    actions: {
        getLoginStore() {
            return useLoginStore();
        },
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("packages/list"),params)
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
                     this.addedPackage(response.data.data);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));

        },
        updatePackage: async function (params) {

            return (await AxiosHelper.patch(AppsbdURL.route('packages', params.id),params)
                .then(response => {
                     this.updatedPackage(params.id,response.data.data);
                     return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        deletePackage: async function (params) {
            return (await AxiosHelper.delete(AppsbdURL.route( "packages",params.package_id))
                .then(response => {
                    this.deletedPackage(params.package_id);
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
        addedPackage(packageData)
        {
            this.getLoginStore().packageList.push(packageData);
        },
        updatedPackage(id, updatedPackage) {
            const index = this.getLoginStore().packageList.findIndex(sta => sta.id === id);
            if (index !== -1) {
                this.getLoginStore().packageList[index] = updatedPackage
            }
        },
        deletedPackage(id) {
            const index = this.getLoginStore().packageList.findIndex(sta => sta.id === id);
            if (index !== -1) {
                this.getLoginStore().packageList.splice(index,1);
            }
        },
    },
})
