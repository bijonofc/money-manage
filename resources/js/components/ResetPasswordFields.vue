<template>
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 w-100 text-center" v-translate>reset.password</h5>
        <span class="apbd-pointer" @click="AppsbdCore.utls.toggleDarkMode()">
                  <i class="apb" :class="AppsbdCore.AppData.darkMode ? 'apb-moon-with-one-star' : 'apb-sun-02'"></i>
                </span>
    </div>

    <Form @submit="onSubmit" class="d-flex flex-column gap-3">
        <InputField
            name="password"
            rules="required"
            label="password"
            type="password"
            v-model="password"
        />

        <InputField
            name="password_confirmation"
            rules="required|confirmed:@password"
            label="confirm.password"
            type="password"
            v-model="password_confirmation"
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
    </Form>
</template>
<script setup>
import { Form } from 'vee-validate'
import { AbInputField as InputField, AbResponseMsg as ResponseMsg } from '@appsbd/vue3-appsbd-ui'
import SmallLoader from '@/components/SmallLoader.vue'
import AppsbdCore from "@/libs/AppsbdCore.js";

const props = defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits([
    'submit',
    'update:password',
    'update:password_confirmation',
])

const password = defineModel('password')
const password_confirmation = defineModel('password_confirmation')

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

