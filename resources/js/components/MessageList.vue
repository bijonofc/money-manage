<template>
    <div class="message-list">
        <!-- Group messages by date -->
        <div v-for="(messages, date) in groupedMessages" :key="date" class="date-group">

            <!-- Date header -->
            <div class="date-header">
                {{ AppHelper.formatDate(date) }}
            </div>

            <!-- Messages of this date -->
            <MessageItem
                v-for="msg in messages"
                :key="msg.id"
                :item="msg"
                :currentUserId="currentUserId"
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import AppHelper from "@/libs/AppHelper.js";
import MessageItem from "./MessageItem.vue";

const props = defineProps({
    items: { type: Array, required: true },
    currentUserId: { type: Number, required: true },
});

// group by date
const groupedMessages = computed(() => {
    return props.items.reduce((groups, msg) => {
        const date = AppHelper.formatDate(msg.created_at);
        if (!groups[date]) groups[date] = [];
        groups[date].push(msg);
        return groups;
    }, {});
});
</script>

<style scoped>
.message-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.date-header {
    text-align: center;
    font-size: 0.8rem;
    font-weight: 500;
    color: #6b7280;
    margin: 12px 0;
}
</style>

