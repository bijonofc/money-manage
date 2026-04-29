<template>
    <div class="row row-cols-1 g-3">

        <div class="col" v-if="!isForce">
            <InputField
                name="old_password"
                rules="required"
                label="old.password"
                placeholder="⚬⚬⚬⚬⚬⚬⚬"
                prefix="Old password"
                type="password"
                v-model="props.formData.old_password"
            />
        </div>

        <!-- New Password -->
        <div class="col">
            <InputField
                name="new_password"
                rules="required"
                label="new.password"
                placeholder="⚬⚬⚬⚬⚬⚬⚬"
                prefix="New Password"
                type="password"
                v-model="props.formData.new_password"
            />
        </div>

        <!-- Password Confirmation -->
        <div class="col">
            <InputField
                name="new_password_confirmation"
                rules="required|confirmed:@new_password"
                label="new.password.confirmation"
                placeholder="⚬⚬⚬⚬⚬⚬⚬"
                prefix="Password Confirm"
                type="password"
                v-model="props.formData.new_password_confirmation"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, toRefs } from 'vue'
import {InputField} from '@appsbd/vue3-appsbd-libs'


const props = defineProps({
    formData: { type: Object, required: true },
    isForce: { type: Boolean, default: false },
})
</script>

