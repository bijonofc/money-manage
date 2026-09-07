<template>
  <nav class="navbar custom-navbar shadow-sm">
    <div class="container-fluid d-flex align-items-center justify-content-between px-2 px-sm-3 flex-nowrap w-100">
      <!-- Left side: Toggle button & Page Title / Breadcrumb -->
      <div class="header-left d-flex align-items-center gap-2 gap-sm-3 min-w-0 flex-grow-1 flex-shrink-1">
        <button
          type="button"
          class="btn btn-icon btn-light rounded-circle d-flex align-items-center justify-content-center p-2 flex-shrink-0"
          @click="toggleSidebar"
          :title="dashboardStore.isMini ? 'Expand Sidebar' : 'Collapse Sidebar'"
        >
          <Menu class="w-5 h-5 text-secondary" :size="20" />
        </button>

        <div class="header-title-wrapper d-flex flex-column min-w-0">
          <h6 class="mb-0 fw-bold text-dark text-truncate page-header-title">
            {{ appsbdUtls.translateGettext(pageTitle) }}
          </h6>
        </div>
      </div>

      <!-- Right side: Quick stats/actions, Dark mode, Fullscreen, User dropdown -->
      <div class="header-right d-flex align-items-center gap-1 gap-sm-2 flex-shrink-0 ms-2">
        <!-- Dark Mode Toggle -->
        <button
          type="button"
          class="btn btn-icon btn-light rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0"
          @click="AppsbdCore.utls.toggleDarkMode()"
          :title="AppsbdCore.AppData.darkMode ? 'Light Mode' : 'Dark Mode'"
        >
          <Sun v-if="AppsbdCore.AppData.darkMode" class="text-warning" :size="18" />
          <Moon v-else class="text-secondary" :size="18" />
        </button>

        <!-- Fullscreen Toggle (Visible on tablet & desktop) -->
        <button
          type="button"
          class="btn btn-icon btn-light rounded-circle p-2 d-none d-md-flex align-items-center justify-content-center flex-shrink-0"
          @click="toggleFullScreen"
          :title="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'"
        >
          <Minimize2 v-if="isFullscreen" class="text-secondary" :size="18" />
          <Maximize2 v-else class="text-secondary" :size="18" />
        </button>

        <!-- User Profile Dropdown -->
        <div class="ms-1 ms-sm-2 flex-shrink-0">
          <VDropdown placement="bottom-end" :distance="8" :arrow-padding="12">
            <button
              type="button"
              class="btn btn-light d-flex align-items-center gap-1.5 py-1 px-1.5 px-sm-2 rounded-pill border shadow-none"
            >
              <div class="user-avatar-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
                {{ userInitials }}
              </div>
              <div class="d-none d-md-flex flex-column text-start me-1">
                <span class="user-name fw-semibold text-dark lh-sm text-truncate" style="max-width: 120px;">{{ loggedUser.name || 'User' }}</span>
                <span class="user-role text-muted small lh-sm">{{ loggedUser.role_title || 'Super Admin' }}</span>
              </div>
              <ChevronDown class="text-muted" :size="14" />
            </button>

            <template #popper="{ hide }">
              <div class="user-popover-card p-2 bg-white rounded-3 shadow-lg border" style="min-width: 230px;">
                <div class="px-3 py-2 mb-2 bg-light rounded-2 border">
                  <div class="fw-semibold text-dark text-truncate">{{ loggedUser.name || 'Administrator' }}</div>
                  <div class="text-muted small text-truncate">{{ loggedUser.email || 'admin@example.com' }}</div>
                </div>

                <router-link
                  class="dropdown-nav-item d-flex align-items-center gap-2 py-2 px-3 rounded-2 text-decoration-none text-dark"
                  to="/profile"
                  @click="hide"
                >
                  <User :size="16" class="text-primary" />
                  <span v-translate>Profile</span>
                </router-link>

                <router-link
                  class="dropdown-nav-item d-flex align-items-center gap-2 py-2 px-3 rounded-2 text-decoration-none text-dark"
                  to="/settings"
                  @click="hide"
                >
                  <Settings :size="16" class="text-secondary" />
                  <span v-translate>Settings</span>
                </router-link>

                <hr class="my-2 border-secondary-subtle" />

                <router-link
                  class="dropdown-nav-item text-danger d-flex align-items-center gap-2 py-2 px-3 rounded-2 text-decoration-none"
                  to="/logout"
                  @click="hide"
                >
                  <LogOut :size="16" />
                  <span v-translate>Logout</span>
                </router-link>
              </div>
            </template>
          </VDropdown>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useDashboardStore } from "@/modules/AdminPanel/Dashboard/DashboardStore.js";
import { useLoginStore } from '@/modules/AdminPanel/User/loginStore.js';
import AppsbdCore from '@/libs/AppsbdCore.js';
import appsbdUtls from '@/libs/AppsbdUtls.js';

// Lucide Icons
import {
  Menu,
  Sun,
  Moon,
  Maximize2,
  Minimize2,
  ChevronDown,
  User,
  Settings,
  LogOut,
  Wallet,
} from '@lucide/vue';

const dashboardStore = useDashboardStore();
const loginStore = useLoginStore();
const route = useRoute();

const isFullscreen = ref(false);

const pageTitle = computed(() => route.meta?.title || 'Dashboard');
const loggedUser = computed(() => loginStore.loggedUserData || {});

const userInitials = computed(() => {
  const name = loggedUser.value.name || 'Admin';
  return name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
});

function toggleSidebar() {
  dashboardStore.toggleMenu();
}

function toggleFullScreen() {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen();
    isFullscreen.value = true;
  } else {
    document.exitFullscreen();
    isFullscreen.value = false;
  }
}
</script>

<style lang="scss" scoped>
.custom-navbar {
  height: var(--ab-header-h, 60px);
  min-height: var(--ab-header-h, 60px);
  max-height: var(--ab-header-h, 60px);
  background: var(--ab-card-bg, #ffffff);
  border-bottom: 1px solid var(--ab-border-color, #e5e7eb);
  z-index: 99;
  display: flex !important;
  flex-wrap: nowrap !important;
  align-items: center;

  :deep(.container-fluid),
  .container-fluid {
    height: 100%;
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: center;
    justify-content: space-between;
    max-width: 100% !important;
  }
}

.min-w-0 {
  min-width: 0 !important;
}

.header-left {
  min-width: 0 !important;
  overflow: hidden;
}

.header-title-wrapper {
  min-width: 0 !important;
  overflow: hidden;
}

.page-header-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--ab-body-color, #1f2937);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;

  @media (max-width: 576px) {
    font-size: 0.95rem;
  }
}

.btn-icon {
  width: 36px;
  height: 36px;
  transition: all 0.2s ease;

  &:hover {
    background-color: var(--ab-border-color, #e5e7eb);
  }
}

.user-avatar-circle {
  width: 28px;
  height: 28px;
  font-size: 11px;
  background: linear-gradient(135deg, #4f46e5, #6366f1);
}

.user-name {
  font-size: 0.85rem;
}

.user-role {
  font-size: 0.72rem;
}

.user-popover-card {
  background-color: var(--ab-card-bg, #ffffff);
  color: var(--ab-body-color, #1f2937);
}

.dropdown-nav-item {
  font-size: 0.88rem;
  transition: all 0.15s ease;

  &:hover {
    background-color: rgba(99, 102, 241, 0.08);
  }
}
</style>
