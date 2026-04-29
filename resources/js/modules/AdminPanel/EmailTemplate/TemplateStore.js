import { defineStore } from "pinia";
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'
import AppsbdURL from '@/libs/AppsbdURL.js'


export const useTemplateStore = defineStore("template", {
    state: () => ({
     templates:[]

    }),
    persist:true,
    getters:{

      },
    actions: {


        getTemplateList:async function(params){
            console.log(params);
            return (await AxiosHelper.post(AppsbdURL.route( "templates/list"),params)
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    console.log(error);
                    return  error.response.data;
                }));
        },
        updateEmailTemplate: async function (params) {
            return (await AxiosHelper.patch(AppsbdURL.route('templates', params.k_word),params)
                .then(response => {
                    // this.updatedProject(params.id,response.data.data);
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
            },
        getEmailTemplate: async function (params) {

            return (await AxiosHelper.get(AppsbdURL.route('templates/'+params.key))
                .then(response => {
                    return response.data;
                })
                .catch(error => {
                    return  error.response.data;
                }));
            },










    },
});
