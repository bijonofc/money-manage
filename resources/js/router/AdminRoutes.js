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
import { useLoginStore } from "@/modules/AdminPanel/User/loginStore.js";
import TemplateList from "@/modules/AdminPanel/EmailTemplate/TemplateList.vue";

import Dashboard from "@/modules/AdminPanel/Dashboard/dashboard.vue";
import AccountList from "@/modules/AdminPanel/Accounts/AccountList.vue";
import TransactionList from "@/modules/AdminPanel/Transactions/TransactionList.vue";
import BudgetList from "@/modules/AdminPanel/Budgets/BudgetList.vue";
import SavingsList from "@/modules/AdminPanel/Savings/SavingsList.vue";
import DebtList from "@/modules/AdminPanel/Debts/DebtList.vue";
import CategoryList from "@/modules/AdminPanel/Categories/CategoryList.vue";

import ForgetPass from "@/modules/AdminPanel/User/ForgetPass.vue";
import BasicSettings from "@/modules/AdminPanel/Settings/BasicSettings.vue";
import NotificationSettings from "@/modules/AdminPanel/Settings/NotificationSettings.vue";
import AdminSetting from "@/modules/AdminPanel/Settings/AdminSetting.vue";
import ResetPassword from "@/modules/AdminPanel/User/ResetPassword.vue";

const routes = [
    // Auth Routes
    {
        path: '/login',
        name: "login",
        component: Login,
        meta: { title: 'Login' }
    },
    {
        path: '/forget-pass',
        name: "forgetPass",
        component: ForgetPass,
        meta: { title: 'Forget Password' }
    },
    {
        path: '/reset-password/:token',
        name: 'reset-password',
        component: ResetPassword,
        props: route => ({
            token: route.params.token,
            email: route.query.email
        }),
        meta: { title: 'Reset Password' }
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

    // Main App Top-Level Routes
    {
        path: "/",
        redirect: "/dashboard",
    },
    {
        path: "/dashboard",
        name: "dashboard",
        component: Dashboard,
        meta: { requiresAuth: true, title: 'Dashboard' },
    },
    {
        path: "/accounts",
        name: "accounts",
        component: AccountList,
        meta: { requiresAuth: true, title: 'Accounts' },
    },
    {
        path: "/transactions",
        name: "transactions",
        component: TransactionList,
        meta: { requiresAuth: true, title: 'Transactions' },
    },
    {
        path: "/budgets",
        name: "budgets",
        component: BudgetList,
        meta: { requiresAuth: true, title: 'Budgets' },
    },
    {
        path: "/savings-goals",
        name: "savings-goals",
        component: SavingsList,
        meta: { requiresAuth: true, title: 'Savings Goals' },
    },
    {
        path: "/debts",
        name: "debts",
        component: DebtList,
        meta: { requiresAuth: true, title: 'Debts & Loans' },
    },
    {
        path: "/categories",
        name: "categories",
        component: CategoryList,
        meta: { requiresAuth: true, title: 'Categories' },
    },
    {
        path: "/users",
        name: "user",
        component: UserList,
        meta: { requiresAuth: true, title: "Users" },
    },
    {
        path: "/profile",
        name: "profile",
        component: Profile,
        meta: { requiresAuth: true, title: "Profile" },
    },
    {
        path: "/activity",
        name: "Activity",
        component: ActivityLog,
        meta: { requiresAuth: true, title: "Activity Logs" }
    },
    {
        path: "/email-template",
        name: "EmailTemplate",
        component: TemplateList,
        meta: { requiresAuth: true, title: "Email Template" }
    },

    // Roles & Access
    {
        path: '/role',
        name: 'roles',
        meta: {
            title: "Roles",
            requiresAuth: true,
            caps: ['role-list', 'access-list']
        },
        component: RoleModule,
        redirect: () => {
            if (ACL.checkACL('role-list')) {
                return '/roles';
            } else if (ACL.checkACL('access-list')) {
                return '/role-access';
            } else {
                return '/dashboard';
            }
        },
        children: [
            {
                path: '/roles',
                component: RoleList,
                meta: {
                    title: "Roles",
                    requiresAuth: true,
                    caps: ['role-list'],
                },
            },
            {
                path: '/role-access',
                component: RoleAccess,
                meta: {
                    title: "Roles Access",
                    requiresAuth: true,
                    caps: ['access-list']
                },
            }
        ]
    },

    // Settings
    {
        path: "/settings",
        name: "settings",
        meta: {
            title: "Settings",
            requiresAuth: true
        },
        redirect: '/settings/app-settings',
        component: AdminSetting,
        children: [
            {
                path: "/settings/app-settings",
                name: "app.settings",
                meta: {
                    title: "Settings",
                    requiresAuth: true
                },
                component: BasicSettings,
            },
            {
                path: "/settings/noti-settings",
                name: "admin.noti",
                meta: {
                    title: "Notification Settings",
                    requiresAuth: true
                },
                component: NotificationSettings,
            },
        ]
    },

    // Backwards Compatibility / Legacy Redirects from /admin/*
    { path: '/admin', redirect: '/dashboard' },
    { path: '/admin/dashboard', redirect: '/dashboard' },
    { path: '/admin/accounts', redirect: '/accounts' },
    { path: '/admin/transactions', redirect: '/transactions' },
    { path: '/admin/budgets', redirect: '/budgets' },
    { path: '/admin/savings-goals', redirect: '/savings-goals' },
    { path: '/admin/debts', redirect: '/debts' },
    { path: '/admin/categories', redirect: '/categories' },
    { path: '/admin/users', redirect: '/users' },
    { path: '/admin/roles', redirect: '/roles' },
    { path: '/admin/role-access', redirect: '/role-access' },
    { path: '/admin/profile', redirect: '/profile' },
    { path: '/admin/settings', redirect: '/settings' },
    { path: '/admin/settings/app-settings', redirect: '/settings/app-settings' },
    { path: '/admin/settings/noti-settings', redirect: '/settings/noti-settings' },
    { path: '/admin/activity', redirect: '/activity' },
    { path: '/admin/email-template', redirect: '/email-template' },
    { path: '/admin/forget-pass', redirect: '/forget-pass' },
    { path: '/admin/reset-password/:token', redirect: to => `/reset-password/${to.params.token}` },

    // 404
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
    linkExactActiveClass: "ab-exact-active"
});

adminRoutes.beforeEach((to, from, next) => {
    const store = useLoginStore();
    const isLoggedIn = store.isLoggedIn;

    if (store.isLoggedIn && store.needPassChange) {
        if (to.name !== 'login' && to.name !== 'logout') {
            return next(false);
        }
    }

    if (to.meta.requiresAuth && !isLoggedIn) {
        next({ name: 'login' });
    } else if ((to.name === 'login' || to.name === 'reset-password' || to.name === 'forgetPass') && isLoggedIn) {
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

export default adminRoutes;
