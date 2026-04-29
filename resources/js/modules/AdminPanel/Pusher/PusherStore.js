import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'

export const usePusherStore = defineStore('pusher', {
    state: () => ({
        contactData: {},
    }),



    actions: {
        getData: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("pusher/list"),params)
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
                .get(AppsbdURL.route("pusher",params.id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        deleteContact: async function (params) {
            return (await AxiosHelper
                .delete(AppsbdURL.route("pusher",params.contact_id))
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
                .post(AppsbdURL.route("pusher"),params)
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
                .patch(AppsbdURL.route("pusher",params.id),params)
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
