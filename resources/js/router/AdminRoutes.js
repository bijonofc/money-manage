import { createRouter, createWebHistory } from "vue-router";
import ACL from '@/libs/acl.js';
import { useLoginStore } from "@/modules/AdminPanel/User/loginStore.js";

const routes = [
    // Auth Routes
    {
        path: '/login',
        name: "login",
        component: () => import("@/modules/AdminPanel/User/Login.vue"),
        meta: { title: 'Login' }
    },
    {
        path: '/register',
        name: "register",
        component: () => import("@/modules/AdminPanel/User/Register.vue"),
        meta: { title: 'Register' }
    },
    {
        path: '/forget-pass',
        name: "forgetPass",
        component: () => import("@/modules/AdminPanel/User/ForgetPass.vue"),
        meta: { title: 'Forget Password' }
    },
    {
        path: '/reset-password/:token',
        name: 'reset-password',
        component: () => import("@/modules/AdminPanel/User/ResetPassword.vue"),
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
        component: () => import("@/modules/AdminPanel/Dashboard/dashboard.vue"),
        meta: { requiresAuth: true, title: 'Dashboard' },
    },
    {
        path: "/accounts",
        name: "accounts",
        component: () => import("@/modules/AdminPanel/Accounts/AccountList.vue"),
        meta: { requiresAuth: true, title: 'Accounts' },
    },
    {
        path: "/transactions",
        name: "transactions",
        component: () => import("@/modules/AdminPanel/Transactions/TransactionList.vue"),
        meta: { requiresAuth: true, title: 'Transactions' },
    },
    {
        path: "/budgets",
        name: "budgets",
        component: () => import("@/modules/AdminPanel/Budgets/BudgetList.vue"),
        meta: { requiresAuth: true, title: 'Budgets' },
    },
    {
        path: "/savings-goals",
        name: "savings-goals",
        component: () => import("@/modules/AdminPanel/Savings/SavingsList.vue"),
        meta: { requiresAuth: true, title: 'Savings Goals' },
    },
    {
        path: "/debts",
        name: "debts",
        component: () => import("@/modules/AdminPanel/Debts/DebtList.vue"),
        meta: { requiresAuth: true, title: 'Debts & Loans' },
    },
    {
        path: "/categories",
        name: "categories",
        component: () => import("@/modules/AdminPanel/Categories/CategoryList.vue"),
        meta: { requiresAuth: true, title: 'Categories' },
    },
    {
        path: "/reports",
        name: "reports",
        component: () => import("@/modules/AdminPanel/Reports/ReportsPage.vue"),
        meta: { requiresAuth: true, title: 'Financial Reports' },
    },
    {
        path: "/design-showcase",
        name: "designShowcase",
        component: () => import("@/modules/AdminPanel/DesignShowcase/DesignShowcase.vue"),
        meta: { requiresAuth: false, devOnly: true, title: 'UX Redesign Showcase' },
    },
    {
        path: "/users",
        name: "user",
        component: () => import("@/modules/AdminPanel/User/UserList.vue"),
        meta: { requiresAuth: true, title: "Users" },
    },
    {
        path: "/profile",
        name: "profile",
        component: () => import("@/modules/AdminPanel/User/Profile.vue"),
        meta: { requiresAuth: true, title: "Profile" },
    },
    {
        path: "/activity",
        name: "Activity",
        component: () => import("@/modules/AdminPanel/ActivityLog/ActivityLog.vue"),
        meta: { requiresAuth: true, title: "Activity Logs" }
    },
    {
        path: "/email-template",
        name: "EmailTemplate",
        component: () => import("@/modules/AdminPanel/EmailTemplate/TemplateList.vue"),
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
        component: () => import("@/modules/AdminPanel/Roles/RoleModule.vue"),
        redirect: () => {
            if (ACL.checkACL('role-list')) {
                return '/role/list';
            } else if (ACL.checkACL('access-list')) {
                return '/role/access';
            } else {
                return '/dashboard';
            }
        },
        children: [
            {
                path: 'list',
                name: 'roles.list',
                component: () => import("@/modules/AdminPanel/Roles/RoleList.vue"),
                meta: {
                    title: "Roles",
                    requiresAuth: true,
                    caps: ['role-list'],
                },
            },
            {
                path: 'access',
                name: 'roles.access',
                component: () => import("@/modules/AdminPanel/Roles/RoleAccess.vue"),
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
        component: () => import("@/modules/AdminPanel/Settings/AdminSetting.vue"),
        children: [
            {
                path: "/settings/app-settings",
                name: "app.settings",
                meta: {
                    title: "Settings",
                    requiresAuth: true
                },
                component: () => import("@/modules/AdminPanel/Settings/BasicSettings.vue"),
            },
            {
                path: "/settings/noti-settings",
                name: "admin.noti",
                meta: {
                    title: "Notification Settings",
                    requiresAuth: true
                },
                component: () => import("@/modules/AdminPanel/Settings/NotificationSettings.vue"),
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
    { path: '/roles', redirect: '/role/list' },
    { path: '/role-access', redirect: '/role/access' },
    { path: '/roles/list', redirect: '/role/list' },
    { path: '/roles/access', redirect: '/role/access' },
    { path: '/admin/roles', redirect: '/role/list' },
    { path: '/admin/role-access', redirect: '/role/access' },
    { path: '/admin/role', redirect: '/role' },
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
        component: () => import("@/layouts/NotFound.vue"),
    },
];

const adminRoutes = createRouter({
    history: createWebHistory(),
    routes,
    linkActiveClass: "ab-active apbd-active",
    linkExactActiveClass: "ab-exact-active"
});

adminRoutes.beforeEach((to, from, next) => {
    const isProduction = Boolean(
        import.meta.env.PROD ||
        window.app_settings?.is_prod ||
        window.app_settings?.app_env === 'production'
    );

    const store = useLoginStore();
    const isLoggedIn = store.isLoggedIn;

    if (to.meta.devOnly && isProduction) {
        return next({ name: isLoggedIn ? 'dashboard' : 'login' });
    }

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
