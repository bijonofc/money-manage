<template>
  <span class="user-name">
    {{ username }}
  </span>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
    username: { type: String, required: true }
})
</script>

<style scoped lang="scss">
.user-name {
    font-size: 13px;
    font-weight: 500;
    color: var(--ab-body-color);
    white-space: nowrap;


}
</style>
