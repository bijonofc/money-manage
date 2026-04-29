<template>
    <div v-if="item.type === 'M'" class="message-wrapper" :class="isOwn ? 'own' : 'other'">
        <!-- Bubble -->
        <div class="message-bubble">
            <div class="message-text">{{ item.message }}</div>
        </div>

        <!-- Username + time -->
        <div class="message-meta">
            <user-name :username="item.user?.name" class="user" />
            <span class="time">{{ AppHelper.formatTime(item.created_at) }}</span>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import AppHelper from "@/libs/AppHelper.js";
import UserName from "@/components/UserName.vue";

const props = defineProps({
    item: { type: Object, required: true },
    currentUserId: { type: Number, required: true },
});

const isOwn = computed(() => props.item.user.id === props.currentUserId);
</script>

<style scoped>
.message-wrapper {
    display: flex;
    flex-direction: column;
    margin-bottom: 14px;
    max-width: 70%;
    word-break: break-word;
}

/* Own message align right */
.message-wrapper.own {
    align-self: flex-end;
}

/* Other message align left */
.message-wrapper.other {
    align-self: flex-start;
}

/* Bubble styles */
.message-bubble {
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 0.9rem;
    line-height: 1.4;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
}

/* Own bubble (green) */
.own .message-bubble {
    background: #dcf8c6;
    border-radius: 16px 16px 0 16px;
}

/* Other bubble (white) */
.other .message-bubble {
    background: #ffffff;
    border-radius: 16px 16px 16px 0;
    border: 1px solid #f0f0f0;
}

/* Message text */
.message-text {
    white-space: pre-wrap;
    word-wrap: break-word;
}

/* Meta info */
.message-meta {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    font-size: 0.75rem;
    margin-top: 4px;
    padding: 0 4px;
    color: #6b7280;
}

.message-meta .user {
    font-weight: 500;
    color: #374151;
}

.message-meta .time {
    font-size: 0.7rem;
}
</style>

