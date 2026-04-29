<template>
    <div
        class="user-avatar"
        @mouseenter="hovered = true"
        @mouseleave="hovered = false"
    >
        <div v-if="user.image || user.image_url">
            <img :src="user.image || user.image_url" :alt="userInitial" class="avatar-img" />
        </div>
        <div v-else>
            {{ userInitial }}
        </div>
        <div v-if="hovered && showRemove" class="popover d-flex align-items-center flex-column">
            <span class="user-name">{{ user.name }}</span>
            <span class="remove-btn d-flex align-items-center gap-2"  @click.stop="$emit('remove', user)">
                <i class="apb apb-trash-3"></i>
                <translate>gbl.delete</translate>
            </span>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, defineProps } from 'vue'

const props = defineProps({
    user: { type: Object, required: true },
    showRemove: { type: Boolean, default: false }
})

const hovered = ref(false)

const userInitial = computed(() => props.user.name?.charAt(0).toUpperCase() || '?')
</script>

<style scoped lang="scss">
.user-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    font-size: 14px;
    font-weight: bold;
    color: #fff;
    border-radius: 50%;
    position: relative;
    cursor: pointer;
    background-color: var(--user-bg, #ccc);

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .popover {
        position: absolute;
       // top: 20px;
        left: 50%;
      //  transform: translateX(-50%);
        background: #fff;
        border: 1px solid #ddd;
        padding: 6px 10px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        white-space: nowrap;
        z-index: 10;

        .user-name {
            font-weight: 500;
            color: #333;
        }
        .remove-btn {
            display: flex;
            align-items: center;
            gap: 4px;
            background: #f44336;
            color: #fff;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 12px;
            cursor: pointer;
            transition: background 0.2s;

            &:hover {
                background: #d32f2f;
            }

            i {
                font-size: 14px;
            }
        }
    }
}
</style>
