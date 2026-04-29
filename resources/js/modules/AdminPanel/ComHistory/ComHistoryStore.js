import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'

export const useComHistoryStore = defineStore('com-history', {
    state: () => ({

    }),



    actions: {
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("com-history/list"),params)
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
                .get(AppsbdURL.route("com-history",params.com_history_id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        addHistory: async function (params) {

            return (await AxiosHelper.post(AppsbdURL.route( "com-history"),params)
                .then(response => {

                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));

        },
    },
})
