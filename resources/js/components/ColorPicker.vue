<template>
    <div class="color-picker">
        <label v-if="label" class="form-label" v-translate>{{ label }}</label>
        <div class="d-flex flex-wrap gap-2">
            <div v-for="(color, index) in colors" :key="index" class="color-swatch" :style="{ backgroundColor: color.color,border: modelValue === color.color ? '1px solid #fefefe' : '1px solid #ccc'  }"
                @click="$emit('update:modelValue', color.color)"
            >
                <span v-if="modelValue === color.color" class="selected-check">✔</span>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: ''
    }
})

defineEmits(['update:modelValue'])

// fixed color set (unchanged)
const colors = [
    { label: "SKIN", name: "default", color: '#2563eb' },
    { label: "SKIN", name: "cyan", color: '#00acc1' },
    { label: "SKIN", name: "green", color: '#4caf50' },
    { label: "SKIN", name: "purple", color: '#7b1fa2' },
    { label: "SKIN", name: "violet", color: '#8C59F6' },
    { label: "SKIN", name: "pink", color: '#d2597d' },
    { label: "SKIN", name: "red", color: '#b63431' },
    { label: "SKIN", name: "orange", color: '#d47500' },
    { label: "SKIN", name: "gray", color: '#757575' },
    { label: "SKIN", name: "black", color: '#323232' }
]
</script>

<style scoped lang="scss">
.color-picker .color-swatch {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: border 0.2s;
}

.color-picker .selected-check {
    color: #e0e0e0;
    font-size: 15px;
    font-weight: bold;
   // text-shadow: 1px 1px 2px #d7d3d3;
}
</style>
