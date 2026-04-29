import {defineStore} from "pinia";
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from "@/libs/AppsbdAxiosHelper";

export const useAgentStore = defineStore('agentUser', {
    state: () => ({
        agentList: [],
    }),
    persist: true,
    getters: {},
    actions: {
        getAgents: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("agents/list"), params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        getAgentWithdrawals: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("agent/withdrawal/list"), params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        getWithdrawDetails: async function (params) {
            return (await AxiosHelper
                .get(AppsbdURL.route("agent/withdrawal/details",params.withdraw_id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        updateAgentWithdrawals: async function (params) {
            return (await AxiosHelper
                .patch(AppsbdURL.route("agent/withdrawal",params.withdraw_id), params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        getAgentBadges: async function (params) {
            return (await AxiosHelper
                .post(AppsbdURL.route("agents/badges/list"), params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error.message);
                    return error.response.data;
                }));
        },
        addAgent: async function (params) {
            return (await AxiosHelper.post(AppsbdURL.route( "agents"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        getAgent: async function (params){
            return (await AxiosHelper.get(AppsbdURL.route( "agents",params.id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        updateAgent: async function (params){
            return (await AxiosHelper.patch(AppsbdURL.route('agents', params.id),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        deleteAgent: async function (params){
            return (await AxiosHelper.delete(AppsbdURL.route('agents', params.id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        addBadge: async function (params) {
            return (await AxiosHelper.post(AppsbdURL.route( "agents/badges"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        addTopup: async function (params) {
            return (await AxiosHelper.post(AppsbdURL.route( "agent/topup"),params,true)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        getBadge: async function (params) {
            return (await AxiosHelper.get(AppsbdURL.route( "agents/badges",params.id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
        updateBadge: async function (params){
            return (await AxiosHelper.patch(AppsbdURL.route('agents/badges', params.id),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },

        deleteBadge: async function (params){
            return (await AxiosHelper.delete(AppsbdURL.route('agents/badges', params.id))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },

        checkUniqueRef: async function(params){
            return (await AxiosHelper.post(AppsbdURL.route( "agents/check-unique-referral"),params,true)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
        },
    }
})
