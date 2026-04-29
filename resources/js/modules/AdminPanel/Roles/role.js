import {defineStore} from "pinia";
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from "@/libs/AppsbdAxiosHelper";
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";

export const useRoleStore = defineStore('role',{
    state:()=>({
        firstLoaded:false,
        firstAccessLoaded : false,
        accessGridData:null,
        gridData:null,
        resData:{}
    }),
    getters:{
        getRoles(){
            if(this.gridData?.rowdata){
                return this.gridData?.rowdata;
            }
            return [];
        }
    },
    actions: {
        setFirstLoad:async function (status) {
            this.firstLoaded=status;
        },
        setFirstAccessLoad:async function (status) {
            this.firstAccessLoaded=status;
        },
        getLoginStore() {
            return useLoginStore();
        },
        getAccessData: async function () {
            return (await AxiosHelper
                .get(AppsbdURL.route("role-accesses/list"))
                .then(response => {
                    this.firstAccessLoaded = true;
                    this.accessGridData = response.data;
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return null;
                }));
        },
        getData: async function (params) {
                return (await AxiosHelper
                    .post(AppsbdURL.route("roles/list"),params)
                    .then(response => {
                        this.gridData = response.data;
                        return response.data;
                    })
                    .catch(error => {
                        console.log(error.message);
                        return error.response.data;
                    }));
        },
        addRole: async function (role_object) {
            return (await AxiosHelper.post(AppsbdURL.route( "roles"),role_object)
                .then(response => {
                    this.addedRole(response.data.data)
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        resetRole: async function (role_object) {
            return (await AxiosHelper.post(AppsbdURL.route("role-accesses/reset-permission"),role_object)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return null;
                }));
        },
        copyRole: async function (role_object) {
            return (await AxiosHelper.post(AppsbdURL.route("role-accesses/copy-permission"),role_object)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return null;
                }));
        },

        updateRole: async function (role_object) {
            return (await AxiosHelper.patch(AppsbdURL.route('roles', role_object.id),role_object)
                .then(response => {
                    this.updatedRole(role_object.id, response.data.data)
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        deleteRole: async function (props) {
            return (await AxiosHelper.delete(AppsbdURL.route( "roles",props.role_id),props)
                .then(response => {
                    this.deletedRole(props.role_id);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        changeRoleStatus: async function (role_obj) {
            return (await AxiosHelper.post(AppsbdURL.route("role-accesses/status-change"),role_obj)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return null;
                }));
        },
        changePermission: async function (role_obj) {
            return (await AxiosHelper.post(AppsbdURL.route('role-accesses/change-permission'),role_obj)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return AxiosHelper.errorHandler(error);
                }));
        },
        getRoleDetails: async function (role_object) {
            return (await AxiosHelper.get(AppsbdURL.route( "roles",role_object.id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        addedRole(role)
        {
            this.getLoginStore().roleList.push(role);
        },
        deletedRole(id) {

            const index = this.getLoginStore().roleList.findIndex(sta => sta.id === id);
            if (index !== -1) {
                this.getLoginStore().roleList.splice(index,1);
            }
        },
        updatedRole(id, updatedRole) {
            const index = this.getLoginStore().roleList.findIndex(sta => sta.id === id);

            if (index !== -1) {
                this.getLoginStore().roleList[index] =  updatedRole;
            }
        },


    }

})
