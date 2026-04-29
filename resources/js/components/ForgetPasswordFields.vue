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

    <Form @submit="onSubmit">
        <InputField
            name="email"
            rules="required|email"
            label="login.email"
            type="email"
            v-model="email"
        />

        <div class="d-grid mb-3">
            <button
                type="submit"
                class="btn btn-primary d-flex align-items-center gap-2 justify-content-center"
                :disabled="loading"
            >
                <translate>gbl.submit</translate>
                <SmallLoader :show="loading" color="#FFF" />
            </button>
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
import { InputField, ResponseMsg } from '@appsbd/vue3-appsbd-libs'
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

</style>
