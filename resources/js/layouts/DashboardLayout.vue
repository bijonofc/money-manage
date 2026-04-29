<template>
  <div class="app-ui">
    <SideMenu :key="appType" />
    <div class="app-body">
      <div class="app-header">
        <Header :darkMode="darkMode" @toggle-dark-mode="onToggleDarkMode" />
      </div>
      <div class="home-section">
        <div class="main-content-wrap" >
          <RouterView />

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

import Header from "@/components/Header.vue";
import SideMenu from '@/components/SideMenu.vue'
import {computed, defineProps} from 'vue'

const props = defineProps({
    darkMode: Boolean,
    toggleDarkMode: Function
})
const appType = computed(() => window.appType || 'admin')

function onToggleDarkMode() {
    props.toggleDarkMode()
}
</script>

<style scoped lang="scss">

</style>
