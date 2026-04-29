import {defineStore} from "pinia";
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from "@/libs/AppsbdAxiosHelper";
import localDataFilter from "@/libs/LocalDataFilter";

export const useActivityStore = defineStore('activity',{
    state:()=>({
        activityLog:[],
    }),
    getters:{

    },
    actions: {
        getLogs: async function (params) {
                return (await AxiosHelper
                    .post(AppsbdURL.route("activity/list"),params)
                    .then(response => {
                        this.activityLog = response.data?.rowdata;
                        return response.data;
                    })
                    .catch(error => {
                        console.log(error.message);
                        return error.response.data;
                    }));
        },
        getLog: async function (params) {
                return (await AxiosHelper.get(AppsbdURL.route("activity",params.activity_id))
                    .then(response => {
                        return response.data;
                    })
                    .catch(error => {
                        console.log(error.message);
                        return error.response.data;
                    }));
        },
    }

})
