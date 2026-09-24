<template>
    <div class="login-container">
        <div class="login-box">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 w-100 text-center" v-translate>Create Account</h5>
                <span class="apbd-pointer d-flex" @click="AppsbdCore.utls.toggleDarkMode()">
                  <i class="apb" :class="AppsbdCore.AppData.darkMode ? 'apb-moon-with-one-star' : 'apb-sun-02'"></i>
                </span>
            </div>

            <ResponseMsg :message="msgs" />

            <div v-if="isRegistered" class="text-center py-3">
                <div class="mb-3 text-warning">
                    <i class="apb apb-clock-01" style="font-size: 48px;"></i>
                </div>
                <h5 class="fw-bold mb-2" v-translate>Registration Pending Approval</h5>
                <p class="text-muted small mb-4">
                    {{ pendingMessage || 'Thank you for registering! Your account has been submitted and is currently pending administrator approval.' }}
                </p>
                <router-link to="/login" class="btn btn-primary w-100">
                    <translate>Back to Sign In</translate>
                </router-link>
            </div>

            <div v-else>
                <RegisterFields
                    v-model:name="name"
                    v-model:email="email"
                    v-model:username="username"
                    v-model:password="password"
                    v-model:passwordConfirmation="passwordConfirmation"
                    :loading="loading"
                    @submit="handleRegister"
                >
                    <Turnstile ref="turnstile" :site-key="rootData.site_key" @verified="verifiedToken" />
                </RegisterFields>

                <div class="divider" v-translate>login.or</div>

                <google-login-button ref="g_login" :client-id="rootData.gl_client_id" :callback="registerWithGoogle" />

                <div class="text-center mt-4">
                    <span class="text-muted small me-1" v-translate>Already have an account?</span>
                    <router-link to="/login" class="text-decoration-none fw-semibold" v-translate>
                        login.signin
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { inject, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useLoginStore } from './loginStore'
import { AbResponseMsg as ResponseMsg } from '@appsbd/vue3-appsbd-ui'
import RegisterFields from '@/components/RegisterFields.vue'
import GoogleLoginButton from '@/components/GoogleLoginButton.vue'
import Turnstile from '@/components/Turnstile.vue'
import AppsbdCore from '@/libs/AppsbdCore.js'

const rootData = inject('rootData', {})
const store = useLoginStore()
const router = useRouter()

const name = ref('')
const email = ref('')
const username = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const msgs = ref({})
const loading = ref(false)
const isRegistered = ref(false)
const pendingMessage = ref('')

const turnstile = ref(null)
const transToken = ref('')

const verifiedToken = (token) => {
    transToken.value = token
}

onMounted(() => {
    if (store.isLoggedIn) {
        router.push('/')
    }
})

const handleRegister = async () => {
    loading.value = true
    msgs.value = {}

    try {
        const payload = {
            name: name.value,
            email: email.value,
            username: username.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value,
            token: transToken.value,
        }

        const res = await store.register(payload)
        msgs.value = res?.msg || {}
        turnstile.value?.Reload()

        if (res?.status) {
            isRegistered.value = true
            pendingMessage.value = res?.msg?.info?.[0] || ''
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

const registerWithGoogle = async (response) => {
    response.changeStatus(true)
    if (response.access_token) {
        try {
            const res = await store.socialLogin({
                token_id: response.access_token,
                token: transToken.value,
            })
            msgs.value = res?.msg || {}

            if (res?.status) {
                router.push('/dashboard')
            } else if (res?.data?.is_pending) {
                isRegistered.value = true
                pendingMessage.value = res?.msg?.error?.[0] || 'Your account is pending administrator approval.'
            }
        } catch (e) {
            console.error(e)
        } finally {
            response.changeStatus(false)
            turnstile.value?.Reload()
        }
    }
}
</script>

<style scoped>
.login-container {
    min-height: 100vh;
    width: 100%;
    overflow-y: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    background-color: var(--ab-body-bg, #f4f6fa);
}

.login-box {
    width: 100%;
    max-width: 440px;
    margin: auto;
    padding: 30px;
    background: var(--ab-card-bg, #ffffff);
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
}

.divider {
    text-align: center;
    margin: 20px 0;
    position: relative;
    color: #8c9097;
    font-size: 13px;
}

.divider:before,
.divider:after {
    content: "";
    position: absolute;
    top: 50%;
    width: 42%;
    height: 1px;
    background-color: #e2e8f0;
}

.divider:before {
    left: 0;
}

.divider:after {
    right: 0;
}
</style>
