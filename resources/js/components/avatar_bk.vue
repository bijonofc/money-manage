<template>
    <div class="user-avatar" @mouseenter="hovered = true" @mouseleave="hovered = false">
        <template v-if="user.image || user.image_url">
            <img :src="user.image || user.image_url" :alt="user.name" class="avatar-img" />
        </template>
        <template v-else>
            {{ userInitial }}
        </template>


        <div v-if="hovered && showRemove" class="popover">
            <!--            <img  v-if="user.image" :src="user.image" alt="" class="popover-img" />-->

            <div class="popover-name">{{ user.name }}</div>
            <span v-if="showRemove && hovered"  @click.stop="$emit('remove', user)">
                <i class="apb apb-trash-3 "></i>
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
    font-size: 12px;
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

    .remove-icon {
        visibility: visible;
        position: absolute;
        bottom: 10px;
        right: 8px;
        width: 16px;
        height: 16px;
        font-size: 8px;
        background: var(--ab-danger);
        color: var(--ab-white);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 20;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.3);
        transition: transform 0.2s ease;

        &:hover {
            transform: scale(1);
        }
    }

    /* Popover */
    .popover {
        position: absolute;
        left: 2%;
        background: var(--ab-card-bg);
        padding: 8px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        min-width: 120px;
        text-align: center;
        z-index: 10;
    }


    .popover-name {
        font-size: 13px;
        font-weight: 500;
        color: var(--ab-body-color);
    }
}
</style>
