<template>
  <span class="status-badge" :style="{ backgroundColor: color || '#ccc'}">
    {{ text }}
  </span>
</template>

<script setup>
defineProps({
    color: String,
    text: String
})
</script>

<style scoped>
.status-badge {
    cursor: pointer;
    display: inline-block;
    padding: 0.20rem 0.30rem;
    font-size: 0.675rem;
    font-weight: 500;
    color: #fff;
    border-radius: 0.20rem;
    white-space: nowrap;
}
</style>

