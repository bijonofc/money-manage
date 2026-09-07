import {defineStore} from "pinia";
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from "@/libs/AppsbdAxiosHelper";
import localDataFilter from "@/libs/LocalDataFilter";

export const useSettingStore = defineStore('setting',{
    state:()=>({
        activityLog:[],
        settingsList:[],
    }),
    persist:true,
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
        getSettings: async function (params = {}) {
            return (await AxiosHelper
                .post(AppsbdURL.route("settings/list"), params)
                .then(response => {
                    const rowdata = response.data?.rowdata || response.data?.data?.rowdata || response.data?.data || [];

                    const grouped = {};
                    if (Array.isArray(rowdata)) {
                        rowdata.forEach(item => {
                            if (!grouped[item.group_slug]) {
                                grouped[item.group_slug] = {};
                            }
                            grouped[item.group_slug][item.s_key] = item.s_value ?? item.s_val;
                        });
                    }

                    this.settingsList = grouped;
                    return this.settingsList;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response?.data || {};
                }));
        },

        updateSettings: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("settings"), params, true)
                .then(response => {
                    if (response.data?.data?.settings) {
                        this.settingsList = response.data.data.settings;
                    }
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response?.data || {};
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
        getSetting: async function (params) {
                return (await AxiosHelper.get(AppsbdURL.route("settings",params.settings_id))
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
