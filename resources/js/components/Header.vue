<template>
  <nav class="navbar custom-navbar">
    <div class="container-fluid d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <span class="toogle_icon" @click="toggleIcon">
                <i class="apb pointer toggle-menu" :class="dashboardStore.isMini ? 'apb-arrow-right' : 'apb-arrow-left'"></i>
            </span>
            <span>
                {{appsbdUtls.translateGettext(pageTitle)}}
            </span>
<!--            <span class="breadcrumb-container d-flex align-items-center">-->
<!--                <template v-for="(crumb, i) in breadcrumbs" :key="i">-->
<!--                  <router-link v-if="crumb.to" :to="crumb.to" class="breadcrumb-link">{{ crumb.label }}</router-link>-->
<!--                  <span v-else class="breadcrumb-current">{{ crumb.label }}</span>-->
<!--                  <span v-if="i < breadcrumbs.length - 1" class="breadcrumb-separator">/</span>-->
<!--                </template>-->
<!--           </span>-->
        </div>



      <div class="d-flex align-items-center gap-2">
<!--           <span class="nav-link custom-link">-->
<!--               <VDropdown>-->
<!--                   <i class="apb apb-notification-bell header-icon position-relative">-->
<!--                        <span v-if="notificationStore.unreadCount > 0" class="notification-badge">-->
<!--                            {{notificationStore.unreadCount }}-->
<!--                        </span>-->
<!--                   </i>-->
<!--                   <template #popper>-->
<!--                       <NotificationBell  @open-detail="handleOpenDetail" />-->
<!--                   </template>-->
<!--               </VDropdown>-->

<!--          </span>-->
           <span class="nav-link custom-link" @click="AppsbdCore.utls.toggleDarkMode()">
               <i class="header-icon apb" :class="AppsbdCore.AppData.darkMode ? 'apb-moon-with-one-star' : 'apb-sun-02'"></i>

          </span>
        <span class="nav-link custom-link" @click="toggleFullScreen">
          <i class="apb header-icon" :class="iconClass"></i>
        </span>

          <div class="nav-item">
              <VDropdown
                  :distance="5"
                  :skidding="-60"
                  :arrow-padding="24"
                  class=""


              >
                  <!-- This will be the popover reference (for the events and position) -->
                  <span
                      class="d-flex align-items-center gap-2 text-decoration-none"
                      href="#"
                      role="button"
                      id="userDropdown"
                      aria-expanded="false"
                  >
                    <img :src="userImage" class="rounded-circle shadow" alt="User" width="30" height="30" />
                </span>

                  <!-- This will be the content of the popover -->

                  <template #popper>
                      <ul class="dropdown-menu show position-static m-0 shadow-sm">
                          <li>
                              <router-link class="dropdown-item" to="/logout" v-translate>
                                  Logout
                              </router-link>
                          </li>
<!--                          <li>-->
<!--                              <a class="dropdown-item" target="_blank" href="https://nogorpos.com/about">About</a>-->
<!--                          </li>-->
                          <li>
                              <router-link class="dropdown-item" :to="profileRoute" v-translate>Profile</router-link>
                          </li>
<!--                          <li>-->
<!--                              <a class="dropdown-item" target="_blank" href="https://nogorpos.com/contact">Contact</a>-->
<!--                          </li>-->
                      </ul>
                  </template>
              </VDropdown>
          </div>

      </div>
    </div>
  </nav>
</template>

<script setup>
import {useDashboardStore} from "@/modules/AdminPanel/Dashboard/DashboardStore.js";
import { useLoginStore } from '@/modules/AdminPanel/User/loginStore'

const adminStore = useLoginStore()

const activeLoginStore = computed(() => {
    return appType.value === adminStore
})
import defaultLogo from '@/assets/header.jpg';
const dashboardStore=useDashboardStore();


import { useRoute } from 'vue-router';
import { computed, onMounted, ref } from 'vue'

import AppsbdCore from '@/libs/AppsbdCore.js'


import appsbdUtls from '@/libs/AppsbdUtls.js'

const iconClass=ref('apb-minimize')
const darkMode=ref(false)
const appType = computed(() => window.appType || 'admin')
const profileRoute = computed(() => `/${appType.value}/profile`)
const emit = defineEmits(['toggle-dark-mode'])
const props = defineProps({
    darkMode: Boolean
})
function toggleIcon() {
    console.log('called');
    dashboardStore.toggleMenu();
}
const route = useRoute();

const pageTitle = computed(() => route.meta.title || 'Default Title');
const userImage = computed(() => {
    const user = activeLoginStore.value.loggedUserData
    return user?.image_url || defaultLogo
})

function toggleFullScreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen()
        iconClass.value = 'apb-minimize'
    } else {
        document.exitFullscreen()
        iconClass.value = 'apb-maximize'
    }
}

</script>

<style lang="scss" scoped>
.breadcrumb-container {
    font-size: 1rem;
    color: #555;

    .breadcrumb-link {
        color: #007bff;
        text-decoration: none;

        &:hover {
            text-decoration: underline;
        }
    }

    .breadcrumb-current {
        font-weight: 500;
      //  color: #000;
        color:var(--ab-body-color)
    }

    .breadcrumb-separator {
        margin: 0 0.5em;
        color: #999;
    }
}
.notification-badge {
    width: 18px;
    height: 18px;
    position: absolute;
    display: flex;
    top: -10px;
    align-items: center;
    justify-content: center;
    right: -12px;
    background: #dc3545;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 600;
    border-radius: 50%;
    padding: 4px 4px;
    line-height: 1;
    box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
}
</style>
