<template>
  <span class="apbd-pointer" @click="toggle">
    <i class="apb" :class="isDarkMode ? 'apb-moon-with-one-star' : 'apb-sun-02'"></i>
  </span>
</template>

<script setup>
import { inject, computed } from 'vue'
const rootData = inject('rootData');
const isDarkMode = computed(() => rootData?.darkMode);
const toggle = () => rootData?.toggleDarkMode?.();
</script>
