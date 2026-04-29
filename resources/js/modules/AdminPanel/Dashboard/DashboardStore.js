import { defineStore } from "pinia";
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'
import AppsbdURL from '@/libs/AppsbdURL.js'
import {useLoginStore} from '@/modules/AdminPanel/User/loginStore.js';


export const useDashboardStore = defineStore("dashboard", {
    state: () => ({
        isMini: false,
        isLoaded:false,
        initialData:{},

    }),
    actions: {
        toggleMenu() {
            console.log('called from store');
            this.isMini = !this.isMini;
        },
        getLoginStore() {
            return useLoginStore();
        },
        getInitialData:async function(params){
            return (await AxiosHelper.get(AppsbdURL.route( "initial-data"))
                .then(response => {
                    if(response.status)
                    {
                        let data = response.data.data;

                        this.getLoginStore().packageList=response.data?.data?.packageList;
                        this.getLoginStore().customerList=response.data?.data?.customerList;
                        this.getLoginStore().userList=response.data?.data?.userList;
                        this.getLoginStore().badgeList=response.data?.data?.badgeList;
                        this.getLoginStore().roleList=response.data?.data?.roleList;

                        delete data.packageList;
                        delete data.customerList;
                        delete data.userList;
                        delete data.badgeList;
                        delete data.roleList;

                        this.initialData=data;
                        return response.data;
                    }

                })
                .catch(error => {
                    console.log(error.response.data);
                    return  error.response.data;
                }));
        },
        getNotifications:async function(params){
            return (await AxiosHelper.get(AppsbdURL.route( "notifications"))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        getNotificationList:async function(params){
            return (await AxiosHelper.post(AppsbdURL.route( "notifications/list"))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        markNotificationsAsRead:async function(params){
            console.log(params);
            return (await AxiosHelper.post(AppsbdURL.route( "notifications"),{ ids: params })
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        deleteNotifications:async function(params){

            return (await AxiosHelper.delete(AppsbdURL.route( "notifications"),{ ids: params })
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },




    },
});
