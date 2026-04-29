import { createRouter, createWebHistory } from "vue-router";
import NotFound from "@/layouts/NotFound.vue";
import ACL from '@/libs/acl.js';


import PaymentList from "@/modules/AdminPanel/PaymentRef/PaymentList.vue";


import RoutingHub from "@/components/RoutingHub.vue";
import PackageList from "@/modules/AdminPanel/Package/PackageList.vue";
import TempCustomerList from "@/modules/AdminPanel/TempCustomer/TempCustomerList.vue";
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
import ApiList from "@/modules/AdminPanel/ApiManage/ApiList.vue";

import Dashboard from "@/modules/AdminPanel/Dashboard/dashboard.vue";
import ForgetPass from "@/modules/AdminPanel/User/ForgetPass.vue";
import BasicSettings from "@/modules/AdminPanel/Settings/BasicSettings.vue";
import NotificationSettings from "@/modules/AdminPanel/Settings/NotificationSettings.vue";
import AdminSetting from "@/modules/AdminPanel/Settings/AdminSetting.vue";
import ApiDashboard from "@/modules/AdminPanel/ApiManage/ApiDashboard.vue";
import ResetPassword from "@/modules/AdminPanel/User/ResetPassword.vue";
import AgentList from "@/modules/AdminPanel/Agent/AgentList.vue";
import AgentBadgeList from "@/modules/AdminPanel/Agent/AgentBadgeList.vue";
import InvoiceList from "@/modules/AdminPanel/Invoice/InvoiceList.vue";
import TransactionList from "@/modules/AdminPanel/Transaction/TransactionList.vue";
import CompanyLedgerList from "@/modules/AdminPanel/Company/CompanyLedgerList.vue";
import ComHistoryList from "@/modules/AdminPanel/ComHistory/ComHistoryList.vue";
import LogList from "@/modules/AdminPanel/ProvisioningLog/LogList.vue";
import CompanyDashboard from "@/modules/AdminPanel/Company/CompanyDashboard.vue";
import ContactList from "@/modules/AdminPanel/Contact/ContactList.vue";
import PusherList from "@/modules/AdminPanel/Pusher/PusherList.vue";
import AgentWithdrawalsList from "@/modules/AdminPanel/Agent/AgentWithdrawalsList.vue";
import FailedJobList from "@/modules/AdminPanel/Job/FailedJobList.vue";
import JobList from "@/modules/AdminPanel/Job/JobList.vue";
import MessageList from "@/modules/AdminPanel/MessageLog/MessageList.vue";


const adminLoginKey = import.meta.env.VITE_ADMIN_LOGIN_KEY || "upslogin";
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
                path: '/admin/package',
                name: 'Package',
                component: PackageList,
                meta: { requiresAuth: true, title:"Packages" }
            },

            {
                path: "/admin/payments",
                name: "payments",
                meta: { requiresAuth: true,title: 'Payment' },
                component: RoutingHub,
                redirect: '/admin/payments/list',
                children:[
                    {
                        path: '/admin/payments/list',
                        name: 'Payment',
                        component: PaymentList,
                        meta: { requiresAuth: true, title:"Payments" }
                    },
                    {
                        path: "/admin/payments/transactions",
                        name: "transaction",
                        meta: { requiresAuth: true,title: 'Transaction' },
                        component: TransactionList,
                    },
                ]
            },



            {
                path: '/admin/customers',
                name: 'customers',
                component: RoutingHub,
                redirect: '/admin/customers/list',
                meta: { requiresAuth: true,title: 'Customers' },
                children:[
                    {
                        path: '/admin/customers/list',
                        name: 'Customer',
                        component: CustomerList,
                        meta: { requiresAuth: true, title:"Customers List" }
                    },
                    {
                        path: '/admin/customers/temp-customers',
                        name: 'TempCustomer',
                        component: TempCustomerList,
                        meta: { requiresAuth: true, title:"Temp-customer" }
                    },

                ]
            },
            {
                path: "/admin/invoices",
                name: "invoice",
                meta: { requiresAuth: true,title: 'Invoices' },
                component: InvoiceList,
            },
            {
                path: '/admin/company',
                name: 'company',
                meta: { requiresAuth: true,title: 'Company' },
                component: RoutingHub,
                redirect: '/admin/company/dashboard',
                children:[
                    {
                        path: '/admin/company/dashboard',
                        name: 'Company',
                        component:CompanyDashboard ,
                        meta: { requiresAuth: true, title:"Company" }
                    },
                    {
                        path: '/admin/company/ledgers',
                        name: 'CompanyLedger',
                        component: CompanyLedgerList,
                        meta: { requiresAuth: true, title:"Company Ledger" }
                    },
                ]
            },
            {
                path: '/admin/contacts',
                name: 'contacts',
                meta: { requiresAuth: true,title: 'Contacts' },
                component: RoutingHub,
                redirect: '/admin/contacts/list',
                children:[
                    {
                        path: '/admin/contacts/list',
                        name: 'contact-list',
                        component:ContactList ,
                        meta: { requiresAuth: true, title:"Contact" }
                    },
                    {
                        path: '/admin/contacts/com-history',
                        name: 'com-history',
                        meta: {
                            requiresAuth: true,
                            title: 'Com History'
                        },
                        component: ComHistoryList,
                    },
                ]
            },

            {
                path: '/admin/pusher',
                name: 'pusher',
                meta: { requiresAuth: true,title: 'Pusher' },
                component: PusherList,
            },
            {
                path: '/admin/jobs',
                name: 'job',
                meta: { requiresAuth: true,title: 'Job' },
                component: RoutingHub,
                redirect: '/admin/jobs/list',
                children:[
                    {
                        path: '/admin/jobs/list',
                        name: 'job-list',
                        component:JobList ,
                        meta: { requiresAuth: true, title:"Job" }
                    },
                    {
                        path: '/admin/jobs/failed',
                        name: 'failed-job',
                        meta: { requiresAuth: true,title: 'Failed Job' },
                        component: FailedJobList,
                    },
                ]

            },
            {
                path: '/admin/message-log',
                name: 'message-log',
                meta: {
                    requiresAuth: true,
                    title: 'Message Log'
                },
                component: MessageList,
            },

            {
                path: '/admin/agent',
                name: 'Agents',
                redirect: '/admin/agent/list',
                children: [
                    {
                        path: '/admin/agent/list',
                        name: 'Agents List',
                        component: AgentList,
                        meta: {requiresAuth: true, title: "Agent List"}
                    },
                    {
                        path: '/admin/agent/badges',
                        name: 'Agent Badge',
                        component: AgentBadgeList,
                        meta: {requiresAuth: true, title: "Agent Badge"}
                    },
                    {
                        path: '/admin/agent/withdrawals',
                        name: 'Agents Withdrawals',
                        component: AgentWithdrawalsList,
                        meta: {requiresAuth: true, title: "Agent Withdrawals"}
                    },
                ]
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
                path: "/admin/api",
                name: "Apis",
                redirect: '/admin/api/dashboard',
                component: RoutingHub,
                children:[
                    {
                        path: "/admin/api/dashboard",
                        name: "api-dashboard",
                        component: ApiDashboard,
                        meta:{requiresAuth: true,title:'Api Dashboard'},

                    },
                    {
                        path: '/admin/api/list',
                        name: 'Api List',
                        component: ApiList,
                        meta: { requiresAuth: true, title:"Api List" }
                    },
                ]
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
    } else if ((to.name === 'login' || to.name === 'basic'|| to.name === 'PackageRef') && isLoggedIn) {
        next({ name: 'Admin' });
    } else {
        next();
    }
})



export default adminRoutes;
