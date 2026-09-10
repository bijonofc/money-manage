<template>
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 w-100 text-center" v-translate>reset.password</h5>
        <span class="apbd-pointer" @click="AppsbdCore.utls.toggleDarkMode()">
                  <i class="apb" :class="AppsbdCore.AppData.darkMode ? 'apb-moon-with-one-star' : 'apb-sun-02'"></i>
                </span>
    </div>

    <Form @submit="onSubmit">
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

        <div class="d-grid">
            <ab-button
                type="submit"
                color="primary"
                :is-animated="loading"
                :disabled="loading"
                v-translate
            >
                gbl.submit
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

</style>
