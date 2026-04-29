import {defineStore} from "pinia";
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from "@/libs/AppsbdAxiosHelper";
import localDataFilter from "@/libs/LocalDataFilter";

export const useLogStore = defineStore('log',{
    state:()=>({
       logs:[]
    }),
    getters:{

    },
    actions: {
        getLogs: async function (params) {
                return (await AxiosHelper
                    .post(AppsbdURL.route("provisioning-logs/list"),params)
                    .then(response => {

                        return response.data;
                    })
                    .catch(error => {
                        console.log(error.message);
                        return error.response.data;
                    }));
        },
        getLog: async function (params) {
                return (await AxiosHelper.get(AppsbdURL.route("provisioning-logs",params.log_id))
                    .then(response => {
                        return response.data;
                    })
                    .catch(error => {

                        return error.response.data;
                    }));
        },
        deleteLog: async function (params) {
                return (await AxiosHelper.delete(AppsbdURL.route("provisioning-logs",params.log_id))
                    .then(response => {
                        return response.data;
                    })
                    .catch(error => {

                        return error.response.data;
                    }));
        },
    }

})
