<template>
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 w-100 text-center" v-translate>
            login.forget
        </h5>

        <span class="apbd-pointer" @click="AppsbdCore.utls.toggleDarkMode()">
            <i class="apb" :class="AppsbdCore.AppData.darkMode  ? 'apb-moon-with-one-star' : 'apb-sun-02'"></i>
        </span>
    </div>

    <ResponseMsg :message="message" />

    <Form @submit="onSubmit" class="d-flex flex-column gap-3">
        <InputField
            name="email"
            rules="required|email"
            label="login.email"
            type="email"
            v-model="email"
        />

        <slot />

        <div class="d-flex justify-content-center">
            <ab-button
                type="submit"
                color="primary"
                class="submit-btn"
                :is-animated="loading"
                :disabled="loading"
            >
                <translate>gbl.submit</translate>
            </ab-button>
        </div>

        <div class="text-center">
            <router-link :to="backRoute" class="text-decoration-none text-muted">
                <i class="apb apb-chevron-left me-1"></i>
                <translate>gbl.back</translate>
            </router-link>
        </div>
    </Form>
</template>
<script setup>
import { Form } from 'vee-validate'
import { AbInputField as InputField, AbResponseMsg as ResponseMsg } from '@appsbd/vue3-appsbd-ui'
import SmallLoader from '@/components/SmallLoader.vue'
import AppsbdCore from '@/libs/AppsbdCore.js'

const props = defineProps({
    loading: Boolean,
    message: {
        type: Object,
        default: () => ({}),
    },
    backRoute: {
        type: String,
        required: true,
    },
})

const emit = defineEmits([
    'submit',
    'update:email',
])

const email = defineModel('email')

const onSubmit = () => {
    emit('submit')
}
</script>





<style scoped lang="scss">
.submit-btn {
    padding: 0.5rem 1.1rem !important;
    font-weight: 500;
    line-height: 1 !important;
}

:deep(.submit-btn) {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    line-height: 1 !important;
    gap: 6px;
}

:deep(.submit-btn > span) {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    line-height: 1 !important;
}

:deep(.submit-btn span) {
    line-height: 1 !important;
}

:deep(.submit-btn .icon) {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
</style>

