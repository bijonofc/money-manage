<template>
  <div class="app-ui">
    <SideMenu :key="appType" />
    <!-- Mobile Backdrop Overlay -->
    <div
      v-if="!dashboardStore.isMini"
      class="sidebar-backdrop d-lg-none"
      @click="dashboardStore.closeMenu"
      aria-label="Close sidebar"
    ></div>
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
import { computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { useRoute } from 'vue-router';
import Header from "@/components/Header.vue";
import SideMenu from '@/components/SideMenu.vue';
import { useDashboardStore } from "@/modules/AdminPanel/Dashboard/DashboardStore.js";

const props = defineProps({
    darkMode: Boolean,
    toggleDarkMode: Function
});

const dashboardStore = useDashboardStore();
const route = useRoute();
const appType = computed(() => window.appType || 'admin');

function onToggleDarkMode() {
    props.toggleDarkMode();
}

// Auto-close sidebar on route change on mobile devices (< 992px)
watch(() => route.fullPath, () => {
    if (window.innerWidth < 992) {
        dashboardStore.closeMenu();
    }
});

function handleResize() {
    if (window.innerWidth < 992 && !dashboardStore.isMini) {
        dashboardStore.closeMenu();
    }
}

function handleKeyDown(e) {
    if (e.key === 'Escape' && window.innerWidth < 992 && !dashboardStore.isMini) {
        dashboardStore.closeMenu();
    }
}

onMounted(() => {
    if (window.innerWidth < 992) {
        dashboardStore.closeMenu();
    }
    window.addEventListener('resize', handleResize);
    window.addEventListener('keydown', handleKeyDown);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
    window.removeEventListener('keydown', handleKeyDown);
});
</script>

<style scoped lang="scss">
</style>
