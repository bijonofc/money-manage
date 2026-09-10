<template>
    <Form @submit="onSubmit">
        <InputField :is-disable="next=='gpt'" name="email" rules="required" label="login.email" type="email" v-model="email"/>
        <InputField name="password" rules="required" label="password" placeholder="⚬⚬⚬⚬⚬⚬⚬" type="password" v-model="password"/>

        <div class="d-grid mb-3">
            <ab-button type="submit" color="primary" class="w-100" :is-animated="props.loading" :disabled="props.loading">
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

