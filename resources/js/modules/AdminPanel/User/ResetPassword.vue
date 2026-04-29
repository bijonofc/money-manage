<template>
    <div class="login-container">
        <div class="login-box">
            <ResponseMsg :message="msgs" />
            <reset-password-fields v-model:password="password" v-model:password_confirmation="password_confirmation" :loading="loading" @submit="handleResetPassword"/>
            <div class="text-center mt-3">
                <small>
                    <router-link :to="`/${adminLoginKey}`" class="text-decoration-none" v-translate>login.signin</router-link>
                </small>
            </div>
            <Turnstile ref="turnstile" :site-key="rootData.site_key" @verified="verifiedToken" />
        </div>
    </div>
</template>

<script setup>
import { ref,inject } from 'vue'
const adminLoginKey = import.meta.env.VITE_ADMIN_LOGIN_KEY || "upslogin";
import { useRouter } from 'vue-router'
import { useUserStore } from '@/modules/AdminPanel/User/UserStore.js'
import {  ResponseMsg } from '@appsbd/vue3-appsbd-libs'


import ResetPasswordFields from "@/components/ResetPasswordFields.vue";
import Turnstile from "@/components/Turnstile.vue";

const props = defineProps({
    token: String,
    email: String,
})

const rootData = inject('rootData');

const turnstile=ref(null);
const transReloaded=ref(false);
const transToken=ref('');
const password = ref('')
const password_confirmation = ref('')
const msgs = ref({})
const loading = ref(false)

const router = useRouter()
const store = useUserStore()

const handleResetPassword = async () => {
    msgs.value = {}
    loading.value = true
    const payload = {
        token: props.token,
        email: props.email,
        password: password.value,
        password_confirmation: password_confirmation.value,
        trans_token:transToken.value,
    }
    try {
        const res = await store.resetPassword(payload)
        transReloaded.value=false
        turnstile.value?.Reload();
        msgs.value=res.msg;
        if (res.status) {
            setTimeout(() => {
                router.push({ name: 'login' });
            }, 5000);
        }
    } catch (e) {
      console.log(e);
    }
        loading.value = false

}
</script>

<style scoped>
.login-box {
    max-width: 400px;
    margin: 80px auto;
    padding: 30px;
    background: var(--ab-card-bg);
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}
</style>
