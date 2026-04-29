import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'

export const useFailedJobStore = defineStore('failedJob', {
    state: () => ({
        contactData: {},
    }),



    actions: {
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("failed-jobs/list"),params)
                .then(response => {
                    return response;
                })
                .catch(error => {
                    console.log(error);
                    return AxiosHelper.errorHandler(error)
                }));
        },
        getFailedJobDetails: async function (params) {
            return (await AxiosHelper
                .get(AppsbdURL.route("failed-jobs",params.job_id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        deleteJob: async function (params) {
            return (await AxiosHelper
                .delete(AppsbdURL.route("failed-jobs",params.job_id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        addContact: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("contacts"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        updateContact: async function (params) {
            return (await AxiosHelper
                .patch(AppsbdURL.route("contacts",params.id),params)
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
