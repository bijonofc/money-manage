<template>
    <Form @submit="onSubmit">
        <InputField :is-disable="next=='gpt'" name="email" rules="required" label="login.email" type="email" v-model="email"/>
        <InputField name="password" rules="required" label="password" placeholder="⚬⚬⚬⚬⚬⚬⚬" type="password" v-model="password"/>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                <translate>login.signin</translate>
                <small-loader :show="props.loading" color="#FFF"/>
            </button>
        </div>
    </Form>

</template>

<script setup>
import { ref } from 'vue'
import { Form } from 'vee-validate'
import {InputField, ResponseMsg} from '@appsbd/vue3-appsbd-libs'
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

