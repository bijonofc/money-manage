<template>
    <Form @submit="onSubmit" class="d-flex flex-column gap-3">
        <InputField name="name" rules="required" label="Full Name" placeholder="John Doe" type="text" v-model="name"/>
        <InputField name="email" rules="required|email" label="Email Address" placeholder="name@example.com" type="email" v-model="email"/>
        <InputField name="username" rules="required" label="Username" placeholder="johndoe" type="text" v-model="username"/>
        <InputField name="password" rules="required|min:6" label="Password" placeholder="••••••••" type="password" v-model="password"/>
        <InputField name="password_confirmation" rules="required|confirmed:@password" label="Confirm Password" placeholder="••••••••" type="password" v-model="passwordConfirmation"/>

        <slot />

        <div class="d-flex justify-content-center">
            <ab-button
                type="submit"
                color="primary"
                class="register-btn"
                :is-animated="props.loading"
                :disabled="props.loading"
            >
                <translate>Create Account</translate>
            </ab-button>
        </div>
    </Form>
</template>

<script setup>
import { Form } from 'vee-validate'
import { AbInputField as InputField } from '@appsbd/vue3-appsbd-ui'

const props = defineProps({
    loading: Boolean,
})

const emit = defineEmits(['submit'])

const name = defineModel('name')
const email = defineModel('email')
const username = defineModel('username')
const password = defineModel('password')
const passwordConfirmation = defineModel('passwordConfirmation')

const onSubmit = () => emit('submit')
</script>

<style scoped>
.register-btn {
    padding: 0.5rem 1.1rem !important;
    font-weight: 500;
    line-height: 1 !important;
}

:deep(.register-btn) {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    line-height: 1 !important;
    gap: 6px;
}

:deep(.register-btn > span) {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    line-height: 1 !important;
}

:deep(.register-btn span) {
    line-height: 1 !important;
}

:deep(.register-btn .icon) {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
</style>
