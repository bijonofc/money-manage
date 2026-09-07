<template>
    <div :class="['sidemenu', { close: dashboardStore.isMini }]">
        <div class="brand-container">
            <router-link class="brand-logo" to="/dashboard" @click="handleNavClick">
                <div class="brand-logo-content">
                    <img
                        :src="logoIconUrl"
                        class="brand-icon-img"
                        alt="Money Manage Logo"
                    />
                    <transition name="brand-fade">
                        <img
                            v-if="!dashboardStore.isMini"
                            :src="logoTitleUrl"
                            class="brand-title-img"
                            alt="Money Manage"
                        />
                    </transition>
                </div>
            </router-link>
            <!-- Show close button ONLY on small/mini screens (< 992px), hidden on large screen -->
            <div class="brand-actions d-lg-none">
                <button
                    type="button"
                    class="btn btn-icon btn-light rounded-circle sidebar-toggle-btn d-flex align-items-center justify-content-center"
                    @click="dashboardStore.closeMenu"
                    title="Close Sidebar"
                >
                    <ChevronsLeft class="text-secondary" :size="18" />
                </button>
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
                    @navigate="handleNavClick"
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

// Lucide Icons
import {
    LayoutDashboard,
    Wallet,
    ArrowLeftRight,
    PieChart,
    Target,
    CreditCard,
    Tag,
    BarChart3,
    Users,
    Shield,
    Settings,
    Activity,
    Sparkles,
    ChevronsLeft,
    ChevronsRight,
    ChevronLeft,
    ChevronRight,
} from '@lucide/vue';

const { proxy } = getCurrentInstance();
const dashboardStore = useDashboardStore();
const settingStore = useSettingStore();
const currentMenu = ref('');

const getAssetUrl = (path) => {
    const rawBase = window.app_settings?.base_url || '';
    let base = '';
    if (rawBase) {
        try {
            const urlObj = new URL(rawBase, window.location.origin);
            if (urlObj.hostname === window.location.hostname || !rawBase.startsWith('http')) {
                base = rawBase.replace(/\/+$/, '');
            }
        } catch (e) {
            base = rawBase.replace(/\/+$/, '');
        }
    }
    const cleanPath = path.replace(/^\/+/, '');
    return base ? `${base}/${cleanPath}` : `/${cleanPath}`;
};

const logoIconUrl = computed(() => getAssetUrl('logo/logo.png'));
const logoTitleUrl = computed(() => getAssetUrl('logo/moneymanage.png'));

function handleToggle(menuName) {
    currentMenu.value = currentMenu.value === menuName ? '' : menuName;
}

function handleNavClick() {
    if (window.innerWidth < 992) {
        dashboardStore.closeMenu();
    }
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
        iconComponent: markRaw(Sparkles),
        devOnly: true
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
        id: 'reports',
        acl: 'report-list',
        name: 'reports',
        title: 'Reports',
        route: '/reports',
        has_icon: true,
        iconComponent: markRaw(BarChart3)
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

const isProduction = computed(() => {
    return Boolean(
        import.meta.env.PROD ||
        window.app_settings?.is_prod ||
        window.app_settings?.app_env === 'production'
    );
});

const filteredMenus = computed(() => {
    return menus.filter(menu => {
        if (menu.devOnly && isProduction.value) {
            return false;
        }
        return !menu.acl || proxy.$CheckACL(menu.acl);
    });
});
</script>

<style>
.ps {
    max-height: calc(100dvh - var(--ab-header-h) - 5px);
}
.brand-fade-enter-active,
.brand-fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.brand-fade-enter-from,
.brand-fade-leave-to {
    opacity: 0;
    transform: translateX(-6px);
}
</style>
