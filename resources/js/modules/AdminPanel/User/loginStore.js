import { defineStore } from 'pinia'
import AppsbdURL from '@/libs/AppsbdURL.js'
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js'
import EventBus from "@/libs/emitter.js";

export const useLoginStore = defineStore('login', {
    state: () => ({
        countries: [],
        packageList:[],
        userList:[],
        badgeList:[],
        roleList:[],
        customerList:[],
        isLoggedIn: false,
        isLoading: false,
        needPassChange:false,
        loggedUserData: {},

    }),
    persist: {
            key: `${window.appType}_user`,
            storage: sessionStorage,
            paths: [
            'countries',
            'packageList',
            'userList',
            'badgeList',
            'roleList',
            'customerList',
            'isLoggedIn',
            'needPassChange',
            'loggedUserData'
        ]
        },
    getters:{
      getIsLoading(){
          return this.isLoading;
      }
    },
    actions: {
        async v_init() {
            // Just ensure the isLoggedIn flag is accurate based on persisted data
            this.isLoggedIn = !!this.loggedUserData?.id
        },

        async loadCountries() {
            try {
                const response =  await fetch("https://restcountries.com/v3.1/all?fields=name,cca2,postalCode");
                this.countries = await response.json()
                return this.countries;
            } catch {
                this.countries = []
            }
        },

        async loadCsrf() {
            try {
                await AxiosHelper.get(AppsbdURL.route('sanctum/csrf-cookie', '', false))
            } catch (error) {
                console.error(error)
            }
        },

        async login(params) {

            await this.loadCsrf()
            try {
                const response = await AxiosHelper.public_post(AppsbdURL.route('user/login'), params)

                if (response.status && response.data?.data?.next=='verify-otp') {
                    this.loggedUserData = response.data.data.user_data
                }else{

                    this.loggedUserData = response.data.data.user_data

                    this.isLoggedIn = true
                    this.needPassChange = response.data.data.user_data.is_force === 'Y'
                }
                return response.data
            } catch (error) {
                return error.response?.data
            }
        },
        verifyOtp: async function (data) {
            return (await AxiosHelper.public_post(AppsbdURL.route('user/verify-otp'), data)
                .then(response => {
                    if (response.data.status) {
                        this.loggedUserData = response.data.data.user_data
                        this.isLoggedIn = true
                        this.needPassChange = response.data.data.user_data.is_force === 'Y'
                    }
                    return response.data;
                })
                .catch(error => {
                    return error.response?.data;
                }));
        },
        resendOtp: async function (data) {
            return (await AxiosHelper.public_post(AppsbdURL.route('user/resend-otp'), data)
                .then(response => {
                    if (response.data.status) {
                        this.loggedUserData = response.data.data.user_data
                    }
                    return response.data;
                })
                .catch(error => {
                    return error.response?.data;
                }));
        },
        storeUserData(param,status)
        {
            this.loggedUserData = param.data;
            this.isLoggedIn = status;
        },
        socialLogin: async function (params) {
            await this.loadCsrf();
            try {
                const response = await AxiosHelper.public_post(AppsbdURL.route('user/social-login'), params)

                if (response.status && response.data?.data?.next=='verify-otp') {
                    this.loggedUserData = response.data.data.user_data
                }else{

                    this.loggedUserData = response.data.data.user_data

                    this.isLoggedIn = true
                    this.needPassChange = response.data.data.user_data.is_force === 'Y'
                }
                return response.data
            } catch (error) {
                return error.response?.data
            }
            /*return (await AxiosHelper.public_post(AppsbdURL.route('user/social-login'), data)
                .then(response => {
                    if (response.data.status)
                    {
                        if (response.status && response.data?.data?.next=='verify-otp') {
                            this.loggedUserData = response.data.data.user_data
                        }else{
                            this.loggedUserData = response.data.data.user_data
                            this.isLoggedIn = true
                            this.needPassChange = response.data.data.user_data.is_force === 'Y'
                        }
                        return response.data
                    }
                    return response.data;
                })
                .catch(error => {
                    console.log(error);
                    try {
                        return error.response.data;
                    }catch (e) {
                        return null;
                    }
                }));*/
        },
        async redirectToLogin(){
            this.logOutLocal();
            this.$router.push({ name: 'login' });
        },
        async forget_password(params) {
            try {
                const response = await AxiosHelper.public_post(AppsbdURL.route('forget-password'), params)
                return response.data
            } catch (error) {
                return error.response?.data
            }
        },

        logOutLocal() {
            this.isLoggedIn = false
            this.loggedUserData = {}
            this.packageList=[]
            this.customerList=[]
            this.userList=[]
            this.roleList=[]
            this.badgeList=[]
            this.needPassChange = false
            this.isLoading=false;
        },

        async logOut() {
            try {
                this.isLoading=true;
                const response = await AxiosHelper.public_post(AppsbdURL.route('user/logout'))
                if (response.status) {
                    this.logOutLocal()
                    this.$router.push({ name: 'login' })
                }
                return response.data
            } catch (error) {
                if (error?.response?.status === 401) {
                    this.logOutLocal()
                    this.$router.push({ name: 'login' })
                }
                return error.response?.data
            }
        },
    },
})
