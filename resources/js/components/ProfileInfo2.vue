<template>
    <div class="profile-info">
        <div v-for="(value, label) in profileFields" :key="label" class="row mb-3">
            <div class="col-md-4 fw-semibold text-capitalize" v-translate>{{ label }}</div>
            <div class="col-md-8 text-muted">{{ value || '-' }}</div>
        </div>

        <div class="row mt-4 g-3" v-if="canShowActions">
            <div class="col-md-6">
                <button class="btn btn-theme w-100" v-if="canUpdate" @click="openProfileModal(user.id)" v-translate>
                    update.profile
                </button>
            </div>
            <div class="col-md-6">
                <button class="btn btn-primary w-100" v-if="canChangePassword" @click="openPasswordModal(user.id)" v-translate>
                    change.password
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    user: { type: Object, required: true },
    canUpdate: { type: Boolean, default: false },
    canChangePassword: { type: Boolean, default: false }
})

const emit = defineEmits(['openProfileModal', 'openPasswordModal'])

const canShowActions = computed(() =>
    props.canUpdate || props.canChangePassword
)
const profileFields = computed(() => ({
    'gbl.name': props.user.name,
    'gbl.username': props.user.username,
    'gbl.email': props.user.email,
    'gbl.contact': props.user.contact_no,
    'gbl.country': props.user.country,
    'gbl.city': props.user.city,
    'gbl.role': props.user.role_name,
    'gbl.state': props.user.state,
    'gbl.zip': props.user.zip_code,
    'gbl.address': props.user.address
}))

function openProfileModal(id) {

    emit('openProfileModal', id)
}
function openPasswordModal(id) {

    emit('openPasswordModal', id)
}
</script>

