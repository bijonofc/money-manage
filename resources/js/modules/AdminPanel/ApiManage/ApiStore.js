import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'

export const useApiStore = defineStore('api', {
    state: () => ({
        initialData:{}
    }),



    actions: {
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("manage-api/list"),params)
                .then(response => {
                    console.log(response.data.data);
                    this.initialData=response.data?.data;
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        getInitialData: async function (params) {
            return (await AxiosHelper
                .get(AppsbdURL.route("manage-api/initial-data"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return error.response.data;
                }));
        },
        addApi: async function (params) {

            return (await AxiosHelper.post(AppsbdURL.route( "manage-api/create"),params)
                .then(response => {
                    // this.addedProject(response.data.data);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));

        },
        updateApi: async function (params) {

            return (await AxiosHelper.patch(AppsbdURL.route('manage-api/update', params.id),params)
                .then(response => {
                    // this.updatedProject(params.id,response.data.data);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        deleteApi: async function (params) {
            return (await AxiosHelper.delete(AppsbdURL.route( "manage-api/delete",params.id))
                .then(response => {
                    // this.deletedProject(params.project_id);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        getApi: async function (params) {
            return (await AxiosHelper.get(AppsbdURL.route( "manage-api/details",params.id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
    },
})
