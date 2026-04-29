<template>
    <div :class="['sidemenu', { close: dashboardStore.isMini }]">
        <div class="brand-container">
            <router-link class="brand-logo" to="/">
                <img :src="appLogo" class="fade-in-element app-logo" />
            </router-link>
            <div class="d-flex align-items-center gap-2">
<!--                <span class="app_name">{{ settingStore.settingsList?.basic_settings?.app_name }}</span>-->
                <span class="toogle_icon" @click="dashboardStore.toggleMenu"><i class="apb pointer toggle-menu" :class="dashboardStore.isMini ? 'apb-arrow-right' : 'apb-arrow-left'"></i></span>
            </div>


        </div>

        <ul :key="appType" class="ul-links">
            <perfect-scrollbar>
                <SidebarItem
                    v-for="menu in baseMenus"
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
import { ref,computed,getCurrentInstance, } from 'vue';
const { proxy } = getCurrentInstance();
import {useDashboardStore} from "@/modules/AdminPanel/Dashboard/DashboardStore.js";
import SidebarItem from './SidebarItem.vue'
import { useRoute } from 'vue-router'

const dashboardStore = useDashboardStore()
import { useSettingStore } from '@/modules/AdminPanel/Settings/SettingStore.js'
import defaultLogo from '@/assets/red_logo.png';
import tooltip from "bootstrap/js/src/tooltip.js";
const settingStore = useSettingStore()
const currentMenu = ref('');
const appType = computed(() => window.appType || 'admin')
const baseMenus = computed(() => {
    switch (appType.value) {
        case 'agent':
            return agentMenus
        case 'admin':
        default:
            return menus
    }
})
const filteredMenus = computed(() => {
    return baseMenus.value.filter(menu => {
        return !menu.acl || proxy.$CheckACL(menu.acl)
    })
})


const route = useRoute()

function handleToggle(menuName) {
    currentMenu.value = currentMenu.value === menuName ? '' : menuName
}
const appLogo = computed(() => {
    return settingStore.settingsList?.basic_settings?.app_logo || defaultLogo;
})
const menus = [
    { id: 'dashboard',acl:'np.dashboard-stat', name: 'dashboard', title: 'Dashboard', route: '/admin', has_icon: true, menu_icon: 'apb apb-dashboard-a' },
    { id:'company',acl:'np.company-dashboard', name: 'company', title: 'Company', route: '/admin/company', has_icon: true, menu_icon: 'apb apb-report5',
        children: [
            { id: 'company-dashboard',acl:'np.company-dashboard', name: 'company', title: 'Company', route: '/admin/company/dashboard', has_icon: true, menu_icon: 'apb apb-report1'},
            { id: 'company-ledger',acl:'np.company-ledger-list', name: 'company-ledger', title: 'Company Ledger', route: '/admin/company/ledgers', has_icon: true, menu_icon: 'apb apb-report-list5' },
        ]
    },
    { id: 'roles', acl:'np.role-list', name: 'roles', title: 'Roles', route: '/admin/roles', has_icon: true, menu_icon: 'apb apb-shield-02' },
    { id: 'user',  acl:'np.user-list', name: 'user', title: 'User', route: '/admin/users', has_icon: true, menu_icon: 'apb apb-users-01' },
    { id: 'agents', acl:'np.agent-list', name: 'agents', title: 'Agents', route: '/admin/agent', has_icon: true, menu_icon: 'apb apb-users-02',
        children: [
            { id: 'agent_list', acl:'np.agent-list', name: 'agent_list', title: 'Agent List', route: '/admin/agent/list',has_icon: true, menu_icon: 'apb apb-users-02' },
            { id: 'agent_badge', acl:'np.agent-badge', name: 'agent_badge', title: 'Agent Badge', route: '/admin/agent/badges',has_icon: true, menu_icon: 'apb apb-badge-percent' },
            { id: 'agent_withdrawals', acl:'np.agent-withdrawal', name: 'agent_withdrawals', title: 'Agent Withdrawals', route: '/admin/agent/withdrawals',has_icon: true, menu_icon: 'apb apb-table-order-list-03' },
        ]
    },
    { id: 'packages', acl:'np.package-list', name: 'packages', title: 'Packages', route: '/admin/package', has_icon: true, menu_icon: 'apb apb-star' },
    { id: 'customers', acl:'np.customer-list', name: 'customers-list', title: 'Customers', route: `/admin/customers`, has_icon: true, menu_icon: 'apb apb-users-03',
        children: [
            { id: 'customers-list', acl:'np.customer-list', name: 'customers', title: 'Customers List', route: `/admin/customers/list`, has_icon: true, menu_icon: 'apb apb-users-03'},
            { id: 'temp-customers', acl:'np.customer-list', name: 'temp-customer', title: 'Temp-customer', route: `/admin/customers/temp-customers`, has_icon: true, menu_icon: 'apb apb-user-plus' },
        ]
    },
    { id: 'pusher', acl:'np.pusher-list', name: 'pusher', title: 'Pusher', route: '/admin/pusher', has_icon: true, menu_icon: 'apb apb-sun-alt' },
    {id: 'apis',  acl:'np.api-list',name: 'api', title: 'Manage Api', route: '/admin/api', has_icon: true, menu_icon: 'apb apb-settings-041',
        children: [
            { id: 'api_dashboard', acl:'np.api-dashboard', name: 'api_dashboard', title: 'Api Dashboard', route: '/admin/api/dashboard',has_icon: true, menu_icon: 'apb apb-Home-01' },
            { id: 'api_list', acl:'np.api-list', name: 'apis', title: 'Api List', route: '/admin/api/list',has_icon: true, menu_icon: 'apb apb-report-list5' },
        ]
    },
    { id: 'payments',acl:'np.payment-list', name: 'payments-list', title: 'Payments', route: '/admin/payments', has_icon: true, menu_icon: 'apb apb-dollar-sign',
    children:
        [
        { id: 'payments-list',acl:'np.payment-list', name: 'payments', title: 'Payments', route: '/admin/payments/list', has_icon: true, menu_icon: 'apb apb-report-list5'},
        { id: 'transactions-list',acl:'np.payment-list', name: 'transactions', title: 'Transaction', route: '/admin/payments/transactions', has_icon: true, menu_icon: 'apb apb-table-order-list-03'},
     ]
    },
    { id: 'invoices',acl:'np.invoice-list', name: 'invoices', title: 'Invoices', route: '/admin/invoices', has_icon: true, menu_icon: 'apb  apb-table-order-list-03' },
    { id: 'contact',acl:'np.contact-list', name: 'contact', title: 'Contact', route: `/admin/contacts`, has_icon: true, menu_icon: 'apb apb-user-square',
        children:
            [
                { id: 'contact-list',acl:'np.contact-list', name: 'contact-list', title: 'gbl.list', route: '/admin/contacts/list', has_icon: true, menu_icon: 'apb apb-report-list1'},
                { id: 'com-history',acl:'np.com-history-list', name: 'com-history', title: 'Com History', route: '/admin/contacts/com-history', has_icon: true, menu_icon: 'apb apb-report-list3' },
            ]
    },
    { id: 'jobs',acl:'np.job-list', name: 'jobs', title: 'Job', route: `/admin/jobs`, has_icon: true, menu_icon: 'apb apb-user-square',
        children:
            [
                { id: 'job-list',acl:'np.job-list', name: 'job-list', title: 'gbl.list', route: '/admin/jobs/list', has_icon: true, menu_icon: 'apb apb-report-list1'},
                { id: 'failed-job', acl:'np.failed-job-list', name: 'failed-job', title: 'Failed Job', route: '/admin/jobs/failed', has_icon: true, menu_icon: 'apb apb-circle-information' },
            ]
    },
    {id: 'activity', acl:'np.activity-list', name: 'activity', title: 'Activity Logs', route: '/admin/activity', has_icon: true, menu_icon: 'apb apb-activity'},

    {id: 'message-log',  acl:'np.message-log-list', name: 'message-log', title: 'Message Log', route: '/admin/message-log', has_icon: true, menu_icon: 'apb apb-mail-04'},
    {id: 'email-template',  acl:'np.template-list', name: 'email-template', title: 'Email-template', route: '/admin/email-template', has_icon: true, menu_icon: 'apb apb-mail-05'},
    {id: 'settings', acl:'np.setting-list', name: 'settings', title: 'Settings', route: '/admin/settings', has_icon: true, menu_icon: 'apb apb-settings-02'},

];
const agentMenus=[
    { id: 'dashboard',acl:'ap.dashboard-stat',  name: 'dashboard', title: 'Dashboard', route: '/agent', has_icon: true, menu_icon: 'apb apb-dashboard-a' },
    {id:'contact',acl: 'ap.contact-list',name:'contact',title:'Contact',route: `/agent/contacts`,has_icon: true, menu_icon: 'apb apb-users-03'},
    {id:'top-up',acl: 'ap.top-up',name:'top-up',title:'Top Up',route: `/agent/top-up`,has_icon: true, menu_icon: 'apb apb-dashboard-a'},
    { id: 'customers', acl:'ap.customer-list', name: 'customers-list', title: 'Customers', route: `/agent/customers`, has_icon: true, menu_icon: 'apb apb-user-square',
        children: [
            { id: 'customers-list', acl:'ap.customer-list', name: 'customers', title: 'Customers List', route: `/agent/customers/list`, has_icon: true, menu_icon: 'apb apb-user-square'},
            { id: 'temp-customers', acl:'ap.customer-list', name: 'temp-customer', title: 'Pending-customer', route: `/agent/customers/temp-customers`, has_icon: true, menu_icon: 'apb apb-user-plus' },
        ]
    },
    { id: 'invoices',acl:'ap.invoice-list', name: 'invoices', title: 'Invoices', route: '/agent/invoices', has_icon: true, menu_icon: 'apb apb-dollar-sign' },
    { id: 'account', acl:'ap.ledger-list', name: 'ledger', title: 'Account', route: '/agent/ledger', has_icon: true, menu_icon: 'apb apb-report-list1',
        children: [
            { id: 'ledger-list', acl:'ap.ledger-list', name: 'ledger', title: 'Balance Ledger', route: '/agent/ledger/list', has_icon: true, menu_icon: 'apb apb-report-list1'},
            { id: 'commission-list', acl:'ap.commission-list', name: 'commission', title: 'Commission Ledger', route: '/agent/ledger/commission-list', has_icon: true, menu_icon: 'apb apb-report-list1'},
            { id: 'unsettled-list', acl:'ap.unsettled-list', name: 'unsettled', title: 'Unsettled Commission', route: '/agent/ledger/unsettled-list', has_icon: true, menu_icon: 'apb apb-report-list1'},
        ]
    },




];
</script>
<style>
.ps{
    max-height: calc( 100dvh - var(--ab-header-h) - 5px);
}
[data-bs-theme="dark"] .app-logo {
    filter: invert(100%) brightness(1000%);
}
</style>
