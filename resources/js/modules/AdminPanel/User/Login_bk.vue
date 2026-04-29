<!-- LoginForm.vue -->
<template>
    <div class="login-container">
        <div class="login-box" v-if="next !== 'verify-otp'">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 w-100 text-center" v-translate>login.signin</h5>
                <span class="apbd-pointer" @click="AppsbdCore.utls.toggleDarkMode()">
                  <i class=" apb" :class="AppsbdCore.AppData.darkMode ? 'apb-moon-with-one-star' : 'apb-sun-02'"></i>
                </span>
            </div>
            <ResponseMsg :message='msgs' />
            <Form @submit="handleLogin" >
                <InputField
                    name="email"
                    rules="required|email"
                    label="login.email"
                    type="email"
                    v-model="email"
                >
                </InputField>
                <InputField
                    name="password"
                    rules="required"
                    label="password"
                    placeholder="⚬⚬⚬⚬⚬⚬⚬"
                    prefix="Password"
                    type="password"
                    v-model="password"
                >
                </InputField>


                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                        <translate>login.signin</translate>
                        <small-loader :show="loading" color="#FFF"/>
                    </button>
                </div>
                <div class="divider" v-translate>login.or</div>
                <div class="d-grid">
                    <div class="d-flex w-100 justify-content-center align-items-center">
                        <google-login class="w-100"  :callback="loginWithGoogle" popupType="TOKEN" :client-id="rootData.gl_client_id" > <!--appsbdDealPlugin?.gl_client_key-->
                            <AnimatedButton ref="g_login"  :is-hide-text-on-animate="false" class="google-btn w-100 d-flex align-items-center justify-content-center" >
                                <svg  viewBox="-3 0 262 262" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid"><path d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027" fill="#4285F4"/><path d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1" fill="#34A853"/><path d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782" fill="#FBBC05"/><path d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251" fill="#EB4335"/></svg>
                                <translate :translate-params="{google:'Google'}">Sign in with %{google}</translate>
                            </AnimatedButton>
                        </google-login>
                    </div>
                </div>
            </Form>
            <div class="text-center mt-4">
                <router-link to="admin/forget-pass" class="text-decoration-none" v-translate>
                    login.forget
                </router-link>
            </div>
        </div>
        <div v-else class="otp-verification">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 w-100 text-center" v-translate>login.otp</h5>
                <span class="apbd-pointer" @click="AppsbdCore.utls.toggleDarkMode()">
                  <i class=" apb" :class="AppsbdCore.AppData.darkMode ? 'apb-moon-with-one-star' : 'apb-sun-02'"></i>
                </span>

            </div>
            <div class="d-flex align-items-center justify-content-center ms-4 me-4">
                <ResponseMsg :message="msgs" />
            </div>
            <Form @submit="onSubmitOtp" class="d-flex flex-column">
                <div class="d-flex justify-content-center mb-3">
                    <div v-for="(digit,index) in otp" :key="index" class="mx-1">
                        <input type="text" maxlength="1" class="form-control text-center"
                               v-model="otp[index]"
                               @input="() => onInput(index)"
                               @keydown.backspace="(e) => onBackspace(e,index)"
                               @paste="onPaste"
                               ref="otpInput"
                        />
                    </div>
                </div>
                <div class="d-flex justify-content-center mb-3">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <span v-translate>gbl.confirm</span>
                        <small-loader :show="isShowLoader" color="#fff"/>
                    </button>
                </div>
                <div class="text-center">
                    <div class="d-flex align-items-center flex-column">
                        <small v-translate>gbl.didnt.receive</small>
                        <select v-model="otp_option" class="form-select form-select-sm w-50 mb-2">
                            <option value="" disabled selected v-translate>gbl.choose</option>
                            <option value="e" v-translate>gbl.email</option>
                            <option value="s" v-translate>gbl.sms</option>
                            <option value="w" v-translate>gbl.whatsapp</option>
                        </select>
                        <button type="button" @click="resendOtp" class="btn btn-primary d-flex align-items-center gap-2">
                            <span v-translate>gbl.resend</span>
                            <small-loader :show="resending" color="#fff"/>
                        </button>
                    </div>

                </div>
            </Form>

        </div>
    </div>

</template>

<script setup>
import {inject, onMounted, ref,computed,nextTick} from 'vue'
import { useRouter } from 'vue-router'
import { useLoginStore } from './loginStore'
import { InputField,ResponseMsg } from '@appsbd/vue3-appsbd-libs'
import { Form } from 'vee-validate'
import AppsbdCore from '@/libs/AppsbdCore.js'
import SmallLoader from '@/components/SmallLoader.vue'
import AnimatedButton from "@/components/AnimatedButton.vue";
import {decodeCredential, GoogleLogin,googleTokenLogin } from 'vue3-google-login'
const rootData = inject('rootData');
import PasswordChange from "@/modules/AdminPanel/User/PasswordChange.vue";
const adminLoginKey = import.meta.env.VITE_ADMIN_LOGIN_KEY || "upslogin";
const email = ref('');
const next = ref('');
const g_login = ref(null)
const msgs = ref({});
const password = ref('');
const is_sso = ref('');
const loading=ref(false);
const store = useLoginStore()
const router = useRouter()
const otpExpire = ref(0)

const appType=inject('appType','admin')
const otp = ref(Array(6).fill(''));
const otpInput = ref([]);
const otp_option = ref('');
const fingerprint = ref(null);
const isShowLoader = ref(false);
const resending = ref(false);
const props=defineProps({
    panel:{
        type:String,
        default:'admin'
    }
})
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
            email:email.value,password:password.value,key:fingerprint.value,panel:props.panel
        }
        if (is_sso=='Y')
        {
            obj.is_sso='Y';
        }
        let res = await store.login(obj);

        msgs.value = res.msg;

        if (res?.status)
        {
            if (res.data?.next === 'verify-otp') {

                next.value = 'verify-otp';
                otpExpire.value = res.data.two_factor_expires_in_minutes;
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
    if (response.access_token)
    {
        g_login.value.showLoader(true);
        const res = await store.socialLogin({token_id:response.access_token});
        msgs.value = res.msg;
        if (res.status) {
            if (res.data?.next)
            {
                next.value=res.data.next;
                is_sso.value='Y';
            }
        }
        router.push('/admin');

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

const onInput = index => {
    if(otp.value[index].length>0 && index<otp.value.length-1)
        otpInput.value[index+1].focus();
}
const onBackspace = (e,index) => { if(otp.value[index]==='' && index>0) otpInput.value[index-1].focus(); }
const onPaste = e => {
    const pastedData = e.clipboardData.getData('text').trim();
    e.preventDefault();
    pastedData.split('').forEach((c,i)=>{if(i<otp.value.length) otp.value[i]=c});
    const filled = Math.min(pastedData.length, otp.value.length);
    if(filled>0 && otpInput.value[filled-1]) otpInput.value[filled-1].focus();
    if(filled===6) onSubmitOtp();
}
const onSubmitOtp = async () => {
    isShowLoader.value = true;
    try {
        fingerprint.value = await get_fingerprint();
        const response = await store.verifyOtp({
            user_id: store.loggedUserData.id,
            code: otp.value.join(''),
            key: fingerprint.value,

        });
        msgs.value = response.msg;
        if (response?.status) {
            router.push('/admin');
        }

    } catch(e){
        console.error(e);
    }
    isShowLoader.value = false;
}
const resendOtp = async () => {
    if(otp_option.value=='')
    {
        msgs.value = {error:['Please select an OTP channel']};
        return;
    }
    msgs.value = {};
    await initialOtp();
    resending.value = true;
    try {
        const response = await store.resendOtp({
            user_id: store.loggedUserData.id,
            otp_option: otp_option.value
        });
      msgs.value = response.msg;

    } catch(e){
        console.error(e);
    }
    resending.value = false;
}

// OTP channel name
const getChannelName = computed(() => {
    const channel = store?.loggedUserData?.otp_channel;
    if(channel==='e') return 'email';
    if(channel==='p') return 'phone number';
    if(channel==='w') return 'whatsapp';
    return 'device';
})
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
.otp-verification{
    max-width: 400px;
    margin: 80px auto;
    padding: 30px;
    background: var(--ab-card-bg);
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}
.otp-verification input.form-control { width: 40px; height: 40px; font-size: 24px; }
.resend-code { font-size: 14px; color: var(--apbd-theme-color); cursor: pointer; }
</style>
