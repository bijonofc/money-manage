import {defineStore} from "pinia";
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from "@/libs/AppsbdAxiosHelper";
import {useDashboardStore} from '@/modules/AdminPanel/Dashboard/DashboardStore.js';
import {useLoginStore} from '@/modules/AdminPanel/User/loginStore.js';

export const useUserStore = defineStore('user',{
    state:()=>({
        userList:[],
    }),
    persist: true,
    getters:{

    },
    actions: {

        getDashboardStore() {
            return useDashboardStore();
        },
        getLoginStore() {
            return useLoginStore();
        },
        getUsers: async function (params) {
                return (await AxiosHelper
                    .post(AppsbdURL.route("users/list"),params)
                    .then(response => {

                        return response.data;
                    })
                    .catch(error => {
                        return error.response.data;
                    }));
        },
        addUser: async function (params) {

            return (await AxiosHelper.post(AppsbdURL.route( "users"),params)
                .then(response => {
                    this.addedUser(response.data.data);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));

        },
        sendResetMail: async function (params) {
            return (await AxiosHelper.post(AppsbdURL.route( "users/reset",params.id),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));

        },
        resetPassword: async function (params) {

            return (await AxiosHelper.public_post(AppsbdURL.route( "users/reset-password"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));

        },
       changePassword: async function (params) {

            return (await AxiosHelper.post(AppsbdURL.route( "users/change-password"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));

        },
        changeGoogleAuth: async function (params) {
            return (await AxiosHelper.post(AppsbdURL.route( "users/change-google-auth"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        updateUser: async function (params) {
            return (await AxiosHelper.patch(AppsbdURL.route('users', params.id),params)
                .then(response => {
                    this.updatedUser(params.id,response.data.data);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        updateProfile: async function (params) {
            return (await AxiosHelper.patch(AppsbdURL.route('user/update'),params)
                .then(response => {
                    this.updatedUser(params.id,response.data.data);
                    this.updateLoginUser(response.data.data);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        updateImage: async function (params) {
            return (await AxiosHelper.post(AppsbdURL.route('users/update-image'),params,true)
                .then(response => {

                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        deleteUser: async function (params) {
            return (await AxiosHelper.delete(AppsbdURL.route( "users",params.user_id))
                .then(response => {

                    this.deletedUser(params.user_id);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        getUser: async function (params) {
            return (await AxiosHelper.get(AppsbdURL.route( "users",params.user_id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        getUserProfile: async function () {
            return (await AxiosHelper.get(AppsbdURL.route( "user/profile"))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        addedUser(user)
        {
           this.getLoginStore().userList.push(user);
        },
        deletedUser(id) {
            const index = this.getLoginStore().userList.findIndex(sta => sta.id === id);
            if (index !== -1) {
                this.getLoginStore().userList.splice(index,1);
            }

        },
        updatedUser(id, updatedUser) {
            const index = this.getLoginStore().userList.findIndex(sta => sta.id === id);
            if (index !== -1) {
                this.getLoginStore().userList[index] =  updatedUser;
            }

        },
        updateLoginUser(updatedUser) {
            this.getLoginStore().loggedUserData.name = updatedUser?.name;
            this.getLoginStore().loggedUserData.email = updatedUser?.email;
            this.getLoginStore().loggedUserData.image = updatedUser?.image;
            this.getLoginStore().loggedUserData.image_url = updatedUser?.image_url;
            this.getLoginStore().loggedUserData.username = updatedUser?.username;
            this.getLoginStore().loggedUserData.contact_no = updatedUser?.contact_no;

        }




    }

})
