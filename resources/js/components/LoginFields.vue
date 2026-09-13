<template>
    <Form @submit="onSubmit" class="d-flex flex-column gap-3">
        <InputField
            :is-disable="next=='gpt'"
            name="email"
            rules="required"
            label="login.email"
            type="email"
            autocomplete="username"
            v-model="email"
        />
        <InputField
            name="password"
            rules="required"
            label="password"
            placeholder="••••••••"
            type="password"
            autocomplete="current-password"
            v-model="password"
        />

        <div class="d-flex justify-content-center pt-1 mb-1">
            <ab-button
                type="submit"
                color="primary"
                class="signin-btn"
                :is-animated="props.loading"
                :disabled="props.loading"
            >
                <translate>login.signin</translate>
            </ab-button>
        </div>
    </Form>
</template>

<script setup>
import { ref } from 'vue'
import { Form } from 'vee-validate'
import { AbInputField as InputField, AbResponseMsg as ResponseMsg } from '@appsbd/vue3-appsbd-ui'
import SmallLoader from '@/components/SmallLoader.vue'
import AppsbdCore from "@/libs/AppsbdCore.js";

const props = defineProps({
    email: String,
    password: String,
    next: String,
    loading: Boolean,

})
const emit = defineEmits(['submit'])

const onSubmit = () => emit('submit')
const email = defineModel('email')
const password = defineModel('password')
</script>

<style scoped>
.signin-btn {
    padding: 0.5rem 1.1rem !important;
    font-weight: 500;
    line-height: 1 !important;
}

:deep(.signin-btn) {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    line-height: 1 !important;
    gap: 6px;
}

:deep(.signin-btn > span) {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    line-height: 1 !important;
}

:deep(.signin-btn span) {
    line-height: 1 !important;
}

:deep(.signin-btn .icon) {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
</style>

