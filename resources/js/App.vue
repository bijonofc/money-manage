<template>

<!--    <DashboardLayout  v-if="store.isLoggedIn && !store.needPassChange" :darkMode="darkMode" :toggleDarkMode="toggleDarkMode"></DashboardLayout>-->
    <div  v-if="store.getIsLoading" class="d-flex h-100 justify-content-center align-items-center flex-column">
        <app-loader msg="loading" />
    </div>
    <template v-else>
        <password-change v-if="store.isLoggedIn && store.needPassChange" :user_id="store.loggedUserData.id" :is_force="true" @close="afterForcePasswordChange"/>

        <DashboardLayout v-else-if="store.isLoggedIn && !store.needPassChange" :darkMode="darkMode" :toggleDarkMode="toggleDarkMode"/>

        <router-view v-else></router-view>
    </template>

</template>

<script setup>
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import { ref } from 'vue'
import PasswordChange from "@/modules/AdminPanel/User/PasswordChange.vue";
import {useRouter} from "vue-router";
import {AppLoader} from "@appsbd/vue3-appsbd-libs";

const store = useLoginStore()
const darkMode = ref(false)
const router=useRouter();
let userToggled = false;
let isShowLoader = false


function toggleDarkMode() {
    userToggled = true

}
function afterForcePasswordChange() {
    console.log('its called');
    store.needPassChange = false;
    router.push({ name: 'dashboard' });
}
</script>
