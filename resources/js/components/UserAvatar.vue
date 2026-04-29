<template>
    <VDropdown
        v-model:shown="isOpen"
        :triggers="[]"
        placement="top"
        :autoHide="false"
    >
        <!-- Avatar -->
        <div
            class="user-avatar"
            @mouseenter="openPopover"
            @mouseleave="closePopoverWithDelay"
        >
            <template v-if="user.image || user.image_url">
                <img
                    :src="user.image || user.image_url"
                    :alt="userInitial"
                    class="avatar-img"
                />
            </template>
            <template v-else>
                {{ userInitial }}
            </template>
        </div>

        <!-- Popover -->
        <template #popper>
            <div @mouseenter="cancelClose" @mouseleave="closePopover">
                <UserPopover :user="user" :showRemove="showRemove" @remove="handleRemove" />
            </div>
        </template>
    </VDropdown>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits } from 'vue'
import { Dropdown as VDropdown } from 'floating-vue'
import UserPopover from '@/components/UserPopover.vue'

const props = defineProps({
    user: { type: Object, required: true },
    showRemove: { type: Boolean, default: false }
})

const emit = defineEmits(['remove'])

const isOpen = ref(false)
let closeTimer = null

const userInitial = computed(() => props.user.name?.charAt(0).toUpperCase() || '?')

function openPopover() {
    clearTimeout(closeTimer)
    isOpen.value = true
}

function closePopover() {
    isOpen.value = false
}

function closePopoverWithDelay() {
    clearTimeout(closeTimer)
    closeTimer = setTimeout(() => (isOpen.value = false), 200)
}

function cancelClose() {
    clearTimeout(closeTimer)
}

function handleRemove(user) {
    emit('remove', user)
    isOpen.value = false
}
</script>

<style scoped lang="scss">
.user-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    font-size: 14px;
    font-weight: 600;
    color: #fff;
    border-radius: 50%;
    cursor: pointer;
    background-color: var(--user-bg, #5c6bc0);
    transition: transform 0.15s ease;

    &:hover {
        transform: scale(1.08);
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
}
</style>
