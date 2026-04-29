
<template>
    <div class="login-container">
        <div class="login-box" v-if="next !== 'verify-otp'">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 w-100 text-center" v-translate>login.signin</h5>
                <span class="apbd-pointer d-flex" @click="AppsbdCore.utls.toggleDarkMode()">
                  <i class=" apb" :class="AppsbdCore.AppData.darkMode ? 'apb-moon-with-one-star' : 'apb-sun-02'"></i>
                </span>
            </div>
            <ResponseMsg :message='msgs' />
            <login-fields v-model:email="email" v-model:password="password" :next="next" :loading="loading" @submit="handleLogin"/>
            <div class="divider" v-translate>login.or</div>
            <google-login-button :is-disabled="next=='gpt'" ref="g_login"  :client-id="rootData.gl_client_id" :callback="loginWithGoogle"/>
            <div class="text-center mt-4">
                <router-link to="admin/forget-pass" class="text-decoration-none" v-translate>
                    login.forget
                </router-link>
            </div>
        </div>
        <OtpVerification
            v-if="next === 'verify-otp'"
            :otp="otp"
            :msgs="msgs"
            v-model:otpOption="otpOption"
            :loading="isShowLoader"
            :resending="resending"

            :expireSeconds="getChannelCountdown(otpOption)"
            :maxResendCount="maxResendCount"
            @submitOtp="onSubmitOtp"
            @resend="resendOtp"
        />
        <Turnstile ref="turnstile" :site-key="rootData.site_key" @verified="verifiedToken" />
    </div>

</template>

<script setup>
import {inject, onMounted, ref,computed,nextTick} from 'vue'
import { useRouter } from 'vue-router'
import { useLoginStore } from './loginStore'
import { ResponseMsg } from '@appsbd/vue3-appsbd-libs'

const rootData = inject('rootData');

import LoginFields from "@/components/LoginFields.vue";
import OtpVerification from "@/components/OtpVerification.vue";
import GoogleLoginButton from "@/components/GoogleLoginButton.vue";
import AppsbdCore from "@/libs/AppsbdCore.js";

const email = ref('');
const next = ref('');
const msgs = ref({});
const password = ref('');
const is_sso = ref('');
const loading=ref(false);
const store = useLoginStore()
const router = useRouter()

const otpExpire = ref(0)
// Channel-wise tracker
const resendTracker = ref({
    e: { count: 0, expiresAt: 0 },
    s: { count: 0, expiresAt: 0 }
})

const otpExpireSeconds = 300 // 5 minutes
const maxResendCount = computed(() => {

    return resendTracker.value[otpOption.value]?.count ?? 0;
});
const appType=inject('appType','admin')
const otp = ref(Array(6).fill(''));
const otpInput = ref([]);
const otpOption = ref('')
const fingerprint = ref(null);
const isShowLoader = ref(false);
const resending = ref(false);
import Turnstile from "@/components/Turnstile.vue";
const turnstile=ref(null);
const transReloaded=ref(false);
const props=defineProps({
    panel:{
        type:String,
        default:'admin'
    }
})
const transToken=ref('');
const verifiedToken = async (token) => {
    transReloaded.value=true;
    transToken.value=token;
};

onMounted(() => {

    if (import.meta.env.MODE === 'development') {
        email.value = 'test@example.com'
        password.value = '12345'
    }
    if (store.isLoggedIn) {
        router.push('/')
    }
})
const handleLogin = async () => {
    loading.value=true;
    try {
        fingerprint.value = await get_fingerprint();
        let obj = {
            email:email.value,
            password:password.value,
            key:fingerprint.value,
            token:transToken.value,
            panel:props.panel
        }
        if (is_sso=='Y')
        {
            obj.is_sso='Y';
        }
        let res = await store.login(obj);
        msgs.value = res.msg;
        transReloaded.value=false
        turnstile.value?.Reload();

        if (res?.status)
        {
            if (res.data?.next === 'verify-otp') {

                next.value = 'verify-otp';
               // otpExpire.value = res.data.retry_after_seconds;
                return;
            }
            router.push('/admin');


        }
    }catch (e) {
        console.log(e);
    }

    loading.value=false;


}

const loginWithGoogle = async (response)=>
{
    response.changeStatus(true);
    if (response.access_token)
    {
        fingerprint.value = await get_fingerprint();
        const res = await store.socialLogin({token_id:response.access_token,key:fingerprint.value});
        msgs.value = res.msg;

        if (res?.status)
        {
            if (res.data?.next === 'verify-otp') {

                next.value = 'verify-otp';
               // otpExpire.value = res.data.retry_after_seconds;
                return;
            }
            router.push('/admin');
        }
        response.changeStatus(false);
    }
}
// --- OTP Functions ---
const initialOtp = async () => {
    otp.value = Array(6).fill('');
    await nextTick();
    if(otpInput.value[0]) otpInput.value[0].focus();
}

const get_fingerprint = async () =>{
    const parts = [
        navigator.userAgent,
        navigator.language,
        screen.width + 'x' + screen.height,
        screen.colorDepth,
        navigator.platform,
        navigator.hardwareConcurrency || '',
        new Date().getTimezoneOffset(),
        navigator.cookieEnabled
    ].join('||');
    const enc = new TextEncoder();
    const buf = await crypto.subtle?.digest('SHA-256', enc.encode(parts));
    const hashArray = Array.from(new Uint8Array(buf));
    return hashArray.map(b => b.toString(16).padStart(2,'0')).join('');
};


// OTP Submit
const onSubmitOtp = async () => {
    isShowLoader.value = true
    try {
        fingerprint.value = await get_fingerprint()
        const response = await store.verifyOtp({
            user_id: store.loggedUserData.id,
            code: otp.value.join(''),
            key: fingerprint.value,
            token:transToken.value,
        })
        msgs.value = response.msg
        transReloaded.value=false
        turnstile.value?.Reload();
        if (response?.status) router.push('/admin')
    } catch (e) { console.error(e) }
    isShowLoader.value = false
}

// OTP resend
const resendOtp = async (channel) => {
    if (!channel) {
        msgs.value = { error: ['Please select OTP channel'] }
        return
    }

    const tracker = resendTracker.value[channel]
    const now = Math.floor(Date.now() / 1000)

    if (now >= tracker.expiresAt) tracker.count = 0

    if (tracker.count >= 2) {
        const channelName = channel === 'e' ? 'Email' : 'SMS'
        msgs.value = {
            error: [`You have reached the maximum resend attempts for ${channelName}. Please wait for the countdown to finish.`]
        }
        return
    }
    msgs.value = {}
    resending.value = true
    try {
        const response = await store.resendOtp({ user_id: store.loggedUserData.id, otp_option: channel,token:transToken.value })
        transReloaded.value=false
        turnstile.value?.Reload();
        if (response.status)
        {
            tracker.count++;
        }
        tracker.expiresAt = response.data.retry_after_seconds?now + response.data.retry_after_seconds: now + otpExpireSeconds;
        msgs.value = response.msg || { success: ['OTP sent successfully'] }
    } catch (e) {
        console.error(e)
        msgs.value = { error: ['Something went wrong'] }
    }


    resending.value = false
}

// Get countdown for child
const getChannelCountdown = (channel) => {
    if (!channel) return 0
    const tracker = resendTracker.value[channel]
    const now = Math.floor(Date.now() / 1000)

    return Math.max(tracker.expiresAt - now, 0)
}
</script>

<style scoped>
body {
    background-color: #f4f6fa;
}

.login-box {
    max-width: 400px;
    margin: 80px auto;
    padding: 30px;
    background: var(--ab-card-bg);
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.logo {
    font-weight: bold;
    font-size: 28px;
    color: #0d6efd;
}

.divider {
    text-align: center;
    margin: 20px 0;
}

.divider:before,
.divider:after {
    content: '';
    display: inline-block;
    width: 40%;
    height: 1px;
    background: #ddd;
    vertical-align: middle;
}

.divider:before {
    margin-right: 10px;
}

.divider:after {
    margin-left: 10px;
}

</style>
