<template>
        <div class="priority-wrapper">
            <span class="priority-pill" :class="'priority-' + priorityClass"></span>
            <span class="text-capitalize" v-if="props.showText">{{ priorityText }}</span>
        </div>
</template>


<script setup>
import {computed} from 'vue'

const props=defineProps({
    priority: { String, required:true},
    showText: { Boolean, default:true}
})
const priorityMap = {
    L: 'low',
    M: 'medium',
    H: 'high',
    U: 'urgent'
}

const priorityClass = computed(() => priorityMap[props.priority] || 'low')
const priorityText = computed(() => priorityClass.value.charAt(0).toUpperCase() + priorityClass.value.slice(1))
</script>

<style scoped lang="scss">
.priority-pill {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;

    &.priority-low {
        background-color: #6bcb77;
    }

    &.priority-medium {
        background-color: #ffd93d;
    }

    &.priority-high {
        background-color: #ff6b6b;
    }

    &.priority-urgent {
        background-color: #c34a36;
    }
}

.priority-wrapper {
    display: flex;
    align-items: center;
}
</style>
