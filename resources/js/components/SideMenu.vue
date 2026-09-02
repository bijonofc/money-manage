<template>
    <div :class="['sidemenu', { close: dashboardStore.isMini }]">
        <div class="brand-container">
            <router-link class="brand-logo" to="/dashboard">
                <img :src="appLogo" class="fade-in-element app-logo" />
            </router-link>
            <div class="d-flex align-items-center gap-2">
                <span class="toogle_icon" @click="dashboardStore.toggleMenu">
                    <i class="apb pointer toggle-menu" :class="dashboardStore.isMini ? 'apb-arrow-right' : 'apb-arrow-left'"></i>
                </span>
            </div>
        </div>

        <ul class="ul-links">
            <perfect-scrollbar>
                <SidebarItem
                    v-for="menu in filteredMenus"
                    :key="menu.id"
                    :menu="menu"
                    :sidebar-closed="dashboardStore.isMini"
                    @toggle="handleToggle(menu.route)"
                />
            </perfect-scrollbar>
        </ul>
    </div>
</template>

<script setup>
import { ref, computed, getCurrentInstance, markRaw } from 'vue';
import { useDashboardStore } from "@/modules/AdminPanel/Dashboard/DashboardStore.js";
import { useSettingStore } from '@/modules/AdminPanel/Settings/SettingStore.js';
import SidebarItem from './SidebarItem.vue';
import defaultLogo from '@/assets/red_logo.png';

// Lucide Icons
import {
    LayoutDashboard,
    Wallet,
    ArrowLeftRight,
    PieChart,
    Target,
    CreditCard,
    Tag,
    Users,
    Shield,
    Settings,
    Activity,
    Sparkles,
} from '@lucide/vue';

const { proxy } = getCurrentInstance();
const dashboardStore = useDashboardStore();
const settingStore = useSettingStore();
const currentMenu = ref('');

const appLogo = computed(() => {
    return settingStore.settingsList?.basic_settings?.app_logo || defaultLogo;
});

function handleToggle(menuName) {
    currentMenu.value = currentMenu.value === menuName ? '' : menuName;
}

const menus = [
    {
        id: 'dashboard',
        name: 'dashboard',
        title: 'Dashboard',
        route: '/dashboard',
        has_icon: true,
        iconComponent: markRaw(LayoutDashboard)
    },
    {
        id: 'design-showcase',
        name: 'designShowcase',
        title: 'UX Showcase',
        route: '/design-showcase',
        has_icon: true,
        iconComponent: markRaw(Sparkles)
    },
    {
        id: 'accounts',
        acl: 'account-list',
        name: 'accounts',
        title: 'Accounts',
        route: '/accounts',
        has_icon: true,
        iconComponent: markRaw(Wallet)
    },
    {
        id: 'transactions',
        acl: 'transaction-list',
        name: 'transactions',
        title: 'Transactions',
        route: '/transactions',
        has_icon: true,
        iconComponent: markRaw(ArrowLeftRight)
    },
    {
        id: 'budgets',
        acl: 'budget-list',
        name: 'budgets',
        title: 'Budgets',
        route: '/budgets',
        has_icon: true,
        iconComponent: markRaw(PieChart)
    },
    {
        id: 'savings-goals',
        acl: 'savings-list',
        name: 'savings-goals',
        title: 'Savings Goals',
        route: '/savings-goals',
        has_icon: true,
        iconComponent: markRaw(Target)
    },
    {
        id: 'debts',
        acl: 'debt-list',
        name: 'debts',
        title: 'Debts & Loans',
        route: '/debts',
        has_icon: true,
        iconComponent: markRaw(CreditCard)
    },
    {
        id: 'categories',
        acl: 'category-list',
        name: 'categories',
        title: 'Categories',
        route: '/categories',
        has_icon: true,
        iconComponent: markRaw(Tag)
    },
    {
        id: 'users',
        acl: 'user-list',
        name: 'users',
        title: 'Users',
        route: '/users',
        has_icon: true,
        iconComponent: markRaw(Users)
    },
    {
        id: 'roles',
        acl: 'role-list',
        name: 'roles',
        title: 'Roles',
        route: '/roles',
        has_icon: true,
        iconComponent: markRaw(Shield)
    },
    {
        id: 'settings',
        acl: 'setting-view',
        name: 'settings',
        title: 'Settings',
        route: '/settings',
        has_icon: true,
        iconComponent: markRaw(Settings)
    },
    {
        id: 'activity',
        acl: 'activity-list',
        name: 'activity',
        title: 'Activity Logs',
        route: '/activity',
        has_icon: true,
        iconComponent: markRaw(Activity)
    },
];

const filteredMenus = computed(() => {
    return menus.filter(menu => {
        return !menu.acl || proxy.$CheckACL(menu.acl);
    });
});
</script>

<style>
.ps {
    max-height: calc(100dvh - var(--ab-header-h) - 5px);
}
[data-bs-theme="dark"] .app-logo {
    filter: invert(100%) brightness(1000%);
}
</style>
