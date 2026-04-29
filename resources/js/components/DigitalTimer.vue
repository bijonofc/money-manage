<template>
    <div class="digital-clock">
        <div class="time">{{ elapsedMs }}</div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";

dayjs.extend(duration);

const props = defineProps({
    elapsedMs: { type: Number, default: 0 },
});


</script>

<style scoped>
.digital-clock {
    //background-color: #f5f5f5;
    padding: 2px 6px;
    display: inline-block;
    color: var(--ab-body-color);
    //border-radius: 8px;
    font-family: 'Courier New', Courier, monospace;
    text-align: center;
}

.digital-clock .time {
    font-size: 16px;
    font-weight: bold;
    color: var(--ab-body-color);
    letter-spacing: 1px;
}
</style>
