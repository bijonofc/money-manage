<template>
    <div class="dot-loader" v-if="show"
         :style="{ '--dot-size': size + 'px', '--dot-color': color }">
        <span></span>
        <span></span>
        <span></span>
    </div>
</template>

<script setup>
import { defineProps,computed } from "vue"

const props = defineProps({
    show: { type: Boolean, default: false },
    color: { type: String, default: "#0d6efd" },
    size: { type: Number, default: 4 }
})

const isLoading = computed(() => {
    return props.show;
})
</script>

<style scoped lang="scss">
.dot-loader {
    display: flex;
    align-items: center;
    gap: 3px;

    span {
        width: var(--dot-size);
        height: var(--dot-size);
        border-radius: 50%;
        background-color: var(--dot-color);
        opacity: 0.4;
        animation: pulse 1.2s infinite ease-in-out;
    }

    span:nth-child(2) { animation-delay: 0.2s; }
    span:nth-child(3) { animation-delay: 0.4s; }
}

@keyframes pulse {
    0%, 80%, 100% { opacity: 0.3; }
    40% { opacity: 1; }
}
</style>
