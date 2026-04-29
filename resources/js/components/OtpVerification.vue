<template>
    <div class="otp-verification">
        <!-- Header -->
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 w-100 text-center" v-translate>login.otp</h5>
            <span class="apbd-pointer" @click="AppsbdCore.utls.toggleDarkMode()">
        <i class="apb"
           :class="AppsbdCore.AppData.darkMode ? 'apb-moon-with-one-star' : 'apb-sun-02'"></i>
      </span>
        </div>

        <!-- Response Messages -->
        <div class="d-flex align-items-center justify-content-center ms-4 me-4">
            <ResponseMsg :message="msgs" />
        </div>

        <!-- OTP Inputs -->
        <Form @submit="submitOtp" class="d-flex flex-column">
            <div class="d-flex justify-content-center mb-3">
                <div v-for="(digit, index) in otp" :key="index" class="mx-1">
                    <input
                        type="text"
                        maxlength="1"
                        class="form-control text-center"
                        v-model="otp[index]"
                        @input="() => onInput(index)"
                        @keydown.backspace="(e) => onBackspace(e, index)"
                        @paste="onPaste"
                        ref="otpInputRefs"
                    />
                </div>
            </div>

            <!-- Confirm Button -->
            <div class="d-flex justify-content-center mb-3">
                <button type="submit" :disabled="loading || !isOtpComplete" class="btn btn-primary d-flex align-items-center gap-2">
                    <span v-translate>gbl.confirm</span>
                    <SmallLoader :show="loading" color="#fff" />
                </button>
            </div>

            <!-- Resend Section -->
            <div class="text-center">
                <div class="d-flex align-items-center flex-column">
                    <small v-translate>gbl.didnt.receive</small>

                    <select v-model="otpOption" class="form-select form-select-sm w-50 mb-2">
                        <option value="" disabled v-translate>gbl.choose</option>
                        <option value="e" v-translate>gbl.email</option>
                        <option value="s" v-translate>gbl.sms</option>
                    </select>

                    <button type="button" @click="resendOtp" class="btn btn-primary d-flex align-items-center gap-2" :disabled="otpOption === '' || (countdown > 0 && maxResendCount >= 2)">
                        <span v-translate>gbl.resend</span>
                        <SmallLoader :show="resending" color="#fff" />
                    </button>

                    <!-- Countdown -->
                    <small v-if="countdown > 0 && maxResendCount >= 2" class="text-danger mt-2">
                        <translate>Resend available in</translate>
                        {{ Math.floor(countdown / 60) }}:
                        {{ String(countdown % 60).padStart(2, '0') }}
                    </small>
                </div>
            </div>
        </Form>
    </div>
</template>

<script setup>
import { ref, watch, nextTick, onUnmounted,computed } from 'vue'
import { Form } from 'vee-validate'
import SmallLoader from '@/components/SmallLoader.vue'
import AppsbdCore from '@/libs/AppsbdCore.js'
import { ResponseMsg } from '@appsbd/vue3-appsbd-libs'

// Props from parent
const props = defineProps({
    otp: Array,
    msgs: Object,
    loading: Boolean,
    resending: Boolean,
    expireSeconds: { type: Number, default: 0 },
    maxResendCount: { type: Number, default: 0 },
})

// Emits to parent
const emit = defineEmits(['submitOtp', 'resend'])

// OTP input refs
const otpInputRefs = ref([])
const otpOption = defineModel('otpOption')

// Countdown
const countdown = ref(props.expireSeconds || 0)
let timer = null
const isOtpComplete = computed(() => {
    return Array.isArray(props.otp) &&
        props.otp.length === 6 &&
        props.otp.every(d => d && d.toString().trim() !== '')
})
// Watch parent expireSeconds
watch(() => props.expireSeconds, (newVal) => {
    countdown.value = newVal
    console.log(countdown.value)
    console.log(props.maxResendCount)
    startCountdown()
})

// Start countdown
const startCountdown = () => {
    if (timer) clearInterval(timer)
    if (countdown.value <= 0) return

    timer = setInterval(() => {
        if (countdown.value > 0) {
            countdown.value--
        } else {
            clearInterval(timer)
            timer = null
        }
    }, 1000)
}

// Cleanup interval on unmount
onUnmounted(() => {
    if (timer) clearInterval(timer)
})

// Input focus handling
const onInput = (index) => {
    if (props.otp[index] && index < props.otp.length - 1) {
        otpInputRefs.value[index + 1]?.focus()
    }
}

const onBackspace = (e, index) => {
    if (props.otp[index] === '' && index > 0) {
        otpInputRefs.value[index - 1]?.focus()
    }
}

const onPaste = (e) => {
    const pastedData = e.clipboardData.getData('text').trim()
    e.preventDefault()

    pastedData.split('').forEach((c, i) => {
        if (i < props.otp.length) props.otp[i] = c
    })

    const filled = Math.min(pastedData.length, props.otp.length)
    if (filled > 0) otpInputRefs.value[filled - 1]?.focus()
    if (filled === props.otp.length) submitOtp()
}

// Emit events
const submitOtp = () => emit('submitOtp')
const resendOtp = () => emit('resend', otpOption.value)
</script>

<style scoped lang="scss">
.otp-verification {
    max-width: 400px;
    margin: 80px auto;
    padding: 30px;
    background: var(--ab-card-bg);
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.otp-verification input.form-control {
    width: 40px;
    height: 40px;
    font-size: 24px;
}
</style>
