import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'

export const useJobStore = defineStore('job', {
    state: () => ({
        jobData: {},
    }),



    actions: {
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("jobs/list"),params)
                .then(response => {
                    return response;
                })
                .catch(error => {
                    console.log(error);
                    return AxiosHelper.errorHandler(error)
                }));
        },
        getJobDetails: async function (params) {
            return (await AxiosHelper
                .get(AppsbdURL.route("jobs",params.job_id))
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
                .delete(AppsbdURL.route("jobs",params.job_id))
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
