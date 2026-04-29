<template>
    <div
        class="color-swatch"
        :style="swatchStyle"
        :title="title"
    >
        <span v-if="selected" class="selected-check">✔</span>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    color: { type: String, required: true },
    selected: { type: Boolean, default: false },
    title: { type: String, default: '' } // optional tooltip
})

const swatchStyle = computed(() => ({
    backgroundColor: props.color,
    border: props.selected ? '2px solid #2563eb' : '1px solid #ccc',
}))
</script>

<style scoped lang="scss">
.color-swatch {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.selected-check {
    color: white;
    font-size: 16px;
    font-weight: bold;
    text-shadow: 0 0 3px rgba(0, 0, 0, 0.7);
}
</style>
