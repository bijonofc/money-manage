import { createRouter, createWebHistory } from "vue-router";
import NotFound from "@/layouts/NotFound.vue";
import ACL from '@/libs/acl.js';

import ActivityLog from "@/modules/AdminPanel/ActivityLog/ActivityLog.vue";
import RoleModule from "@/modules/AdminPanel/Roles/RoleModule.vue";
import RoleAccess from "@/modules/AdminPanel/Roles/RoleAccess.vue";
import RoleList from "@/modules/AdminPanel/Roles/RoleList.vue";
import UserList from "@/modules/AdminPanel/User/UserList.vue";
import Profile from "@/modules/AdminPanel/User/Profile.vue";
import Login from "@/modules/AdminPanel/User/Login.vue";
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import TemplateList from "@/modules/AdminPanel/EmailTemplate/TemplateList.vue";
import CustomerList from "@/modules/AdminPanel/Customer/CustomerList.vue";

import Dashboard from "@/modules/AdminPanel/Dashboard/dashboard.vue";
import ForgetPass from "@/modules/AdminPanel/User/ForgetPass.vue";
import BasicSettings from "@/modules/AdminPanel/Settings/BasicSettings.vue";
import NotificationSettings from "@/modules/AdminPanel/Settings/NotificationSettings.vue";
import AdminSetting from "@/modules/AdminPanel/Settings/AdminSetting.vue";
import ResetPassword from "@/modules/AdminPanel/User/ResetPassword.vue";
import ContactList from "@/modules/AdminPanel/Contact/ContactList.vue";


const routes = [
    {
        path: '/login',
        name: "login",
        component: Login,
        props: { panel: 'admin' },
        meta:{title:'Login'}
    },
    {
        path: '/admin/reset-password/:token',
        name: 'reset-password',
        component: ResetPassword,
        props: route => ({
            token: route.params.token,
            email: route.query.email
        }),
        meta: {
            title: 'Reset Password'
        }
    },

    {
        path: "/",
        redirect: "/admin/",
    },
    {
        path: "/admin",
        name: "Admin",
        redirect: "/admin/",
        /*beforeEnter: (to, from, next) => {
            const store = useLoginStore();
            next({ name: 'login' });
        },*/
        children:[
            {
                path: "/admin/",
                name: "dashboard",
                component: Dashboard,
                meta:{requiresAuth: true,title:'Dashboard'},

            },
            {
                path: '/admin/customers',
                name: 'customers',
                meta: { requiresAuth: true,title: 'Customers' },
                component: CustomerList,
            },
            {
                path: '/admin/contacts',
                name: 'contacts',
                meta: { requiresAuth: true,title: 'Contacts' },
                component: ContactList,
            },
            {
                path: '/admin/activity',
                name: 'Activity',
                component: ActivityLog,
                meta: { requiresAuth: true, title:"Activity Logs" }
            },
            {
                path: '/admin/email-template',
                name: 'EmailTemplate',
                component: TemplateList,
                meta: { requiresAuth: true, title:"Email Template" }
            },
            {
                path: "/admin/users",
                name: "user",
                meta: {
                    title:"User",
                    requiresAuth: true
                },
                component: UserList,
            },
            {
                path: "/admin/settings",
                name: "settings",
                meta: {
                    title:"Settings",
                    requiresAuth: true
                },
                redirect:'/admin/settings/app-settings',
                component: AdminSetting,
                children: [

                    {
                        path: "/admin/settings/app-settings",
                        name: "app.settings",
                        meta: {
                            title:"Settings",
                            requiresAuth: true
                        },
                        component: BasicSettings,
                    },
                    {
                        path: "/admin/settings/noti-settings",
                        name: "admin.noti",
                        meta: {
                            title:"Notification Settings",
                            requiresAuth: true
                        },
                        component: NotificationSettings,
                    },


                ]
            },
            {
                path: "/admin/profile",
                name: "profile",
                meta: {
                    title:"Profile",
                    requiresAuth: true
                },
                component: Profile,
            },
            {
                path: "/admin/forget-pass",
                name: "forgetPass",
                component: ForgetPass,
                meta:{title:'Forget Password'}
            },

            {
                path: '/admin/role',
                name: 'roles',
                meta: {
                    title: "Roles",
                    requiresAuth: true,
                    caps:['role-list','access-list']
                },

                component: RoleModule,
                redirect: (to) => {
                    if (ACL.checkACL('role-list')) {
                        return '/admin/roles'
                    } else if (ACL.checkACL('access-list')) {
                        return '/admin/role-access'
                    }else {
                        return null;
                    }
                },
                children: [
                    {
                        path: '/admin/roles',
                        component: RoleList,
                        meta: {
                            title: "Roles",
                            requiresAuth: true,
                            caps:['role-list'],

                        },
                    },
                    {
                        path: '/admin/role-access',
                        component: RoleAccess,
                        meta: {
                            title: "Roles Access",
                            requiresAuth: true,
                            caps:['access-list']
                        },
                    }
                ]
            },
        ]
    },
    {
        path: "/logout",
        name: "logout",
        beforeEnter: (to, from, next) => {
            const store = useLoginStore();
            store.logOut();
            next({ name: 'login' });
        }
    },

    {
        path: "/:pathMatch(.*)*",
        name: "NotFound",
        component: NotFound,
    },
];


const adminRoutes = createRouter({
    history: createWebHistory(),
  routes,
  linkActiveClass: "ab-active apbd-active",
    linkExactActiveClass:"ab-exact-active"

});

adminRoutes.beforeEach((to, from, next) => {
    const isLoggedIn = useLoginStore().isLoggedIn;
    const store=useLoginStore();
    if (store.isLoggedIn && store.needPassChange) {

        if (to.name !== 'login' && to.name !=='logout') {
            return next(false);
        }

    }
    if (to.meta.requiresAuth && !isLoggedIn) {
        next({ name: 'login' });
    } else if ((to.name === 'login' || to.name === 'reset-password') && isLoggedIn) {
        next({ name: 'Admin' });
    } else {
        next();
    }
})



export default adminRoutes;
