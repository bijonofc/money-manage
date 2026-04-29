import { createApp ,markRaw} from 'vue'
import App from './App.vue'
import "bootstrap/dist/css/bootstrap.min.css";
import "./scss/style.scss";
import './scss/skin/default-color.scss';


import appsbdVeeRules from "./libs/AppsbdVeeRules.js";
import AppsbdUtls from '@/libs/AppsbdUtls.js'


import "bootstrap/dist/css/bootstrap.min.css";
import Multiselect from '@vueform/multiselect';
import '@vueform/multiselect/themes/default.css';
import "vue-toastification/dist/index.css";
import "v-calendar/style.css";
import 'floating-vue/dist/style.css';
import "@appsbd/vue3-appsbd-libs/dist/style.css";
import {vTooltip, vClosePopper, Dropdown, Tooltip, Menu} from 'floating-vue';
import Toast, {POSITION} from "vue-toastification";


import {createAppsbdURL} from '@/libs/AppsbdURL.js'
import EventBus from "@/libs/emitter.js";
import gettext from "@/libs/gettext.js";
import adminRoutes from './router/AdminRoutes.js';
window.appType = 'admin';
import pinia  from '@/libs/store.js';
import ACL from '@/libs/acl.js';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import PerfectScrollbar from 'vue3-perfect-scrollbar'
import 'vue3-perfect-scrollbar/dist/vue3-perfect-scrollbar.css'
import html2pdf from 'vue-html2pdf';

import appGettextComponent from '@/libs/AppGettextComponent.js';
import { createAppsbdCore } from '@/libs/AppsbdCore.js'
import attrOption from "@/libs/attrOption.js";
import vue3GoogleLogin from 'vue3-google-login';

// Fallback for app_settings when running outside blade template (e.g., direct Vite dev)
window.app_settings = window.app_settings || {
    base_url: 'http://localhost:8000',
    api_url: 'http://localhost:8000/api/admin/v1/',
    currencySymbol: '৳',
    locale: 'en',
    site_key: '',
    gl_client_id: '',
};

createAppsbdURL({api_url:app_settings.api_url});
createAppsbdCore({api_url:app_settings.api_url});

pinia.use(({ store }) => {
    store.$router = markRaw(adminRoutes)
})

pinia.use(({ store }) => {
    store.appType = 'admin';
})

const app = createApp(App)
app.config.globalProperties.$appType = 'admin';
app.provide('appType', 'admin');
    app.use(gettext)
    .use(Toast, { position:POSITION.BOTTOM_RIGHT })
    .use(adminRoutes)
    .use(pinia)
    .use(ACL)
    .use(EventBus)
    .use(Multiselect)
    .use(VueSweetalert2)
    .use(PerfectScrollbar, { watchOptions: true,suppressScrollX: false})
    .use(AppsbdUtls,gettext)
    .use(appsbdVeeRules,gettext)
    .use(html2pdf)
    .use(attrOption,app_settings)
    .directive('tooltip', vTooltip)
    .directive('close-popper', vClosePopper)
    .component('translate', appGettextComponent)
    .directive('translate', gettext.appDirective)
    .component('VDropdown', Dropdown)
    .component('VTooltip', Tooltip)
    .component('Menu', Menu)
    if(app_settings) {
        app.use(vue3GoogleLogin, {
            scope: 'profile email country',
            clientId: app_settings?.gl_client_id,
            prompt: false,
        });
    }

    app.mount('#app')


