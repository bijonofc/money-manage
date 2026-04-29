<template>
    <div class="user-popover-wrapper">
        <div class="user-popover-content">
            <!-- User name -->
            <span class="user-name">{{ user.name }}</span>

            <!-- Remove button -->
            <span
                v-if="showRemove"
                class="remove-btn"
                @click.stop="handleRemove"
            >
        <i class="apb apb-trash-3"></i>
        <translate>gbl.delete</translate>
      </span>
        </div>
    </div>
</template>

<script setup>
import { hideAllPoppers } from 'floating-vue'

const props = defineProps({
    user: { type: Object, required: true },
    showRemove: { type: Boolean, default: true }
})

const emit = defineEmits(['remove'])

function handleRemove() {
    emit('remove', props.user)
    hideAllPoppers() // close any open dropdowns/popovers
}
</script>

<style scoped lang="scss">
.user-popover-wrapper {
    background: var(--ab-card-bg, #fff);
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
    padding: 10px;
    min-width: 90px;
    text-align: center;
}

.user-popover-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.user-name {
    font-weight: 600;
    font-size: 13px;
    color: #333;
    margin-bottom: 2px;
}

.remove-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 12px;
    background: #f44336;
    color: #fff;
    border-radius: 4px;
    padding: 5px 10px;
    cursor: pointer;
    transition: all 0.2s ease-in-out;

    &:hover {
        background: #d32f2f;
        transform: translateY(-1px);
    }

    i {
        font-size: 13px;
    }
}
</style>
