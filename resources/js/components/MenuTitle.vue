<template>
  <span class="icon-wrapper" v-tooltip="appsbdUtls.translateGettext(menu.title)">
    <component v-if="menu.iconComponent" :is="menu.iconComponent" :size="20" class="sidebar-icon" />
    <template v-else-if="menu.has_icon && menu.menu_icon">
      <i :class="['sidebar-icon', menu.menu_icon]"></i>
    </template>
    <template v-else-if="isImage(menu.menu_icon)">
      <img :src="menu.menu_icon" alt="icon" class="sidebar-image" />
    </template>
    <template v-else>
      <span class="text-fallback">{{ firstLetter }}</span>
    </template>
  </span>
  <span class="link_name ms-2 flex-grow-1" v-translate>{{ menu.title }}</span>
  <span v-if="menu.children?.length && !sidebarClosed" class="chevron-icon" :class="{ rotate: isOpen }">
    <i class="apb apb-chevron-down arrow"></i>
  </span>
</template>

<script setup>
import { computed } from 'vue';
import appsbdUtls from "@/libs/AppsbdUtls.js";

const props = defineProps({
  menu: {
    type: Object,
    default: () => ({})
  },
  isOpen: Boolean,
  sidebarClosed: Boolean
});

const firstLetter = computed(() =>
  props.menu.title ? props.menu.title.charAt(0).toUpperCase() : '?'
);

function isImage(icon) {
  return typeof icon === 'string' && (icon.startsWith('http') || icon.startsWith('/'));
}
</script>

<style scoped lang="scss">
.icon-wrapper {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  flex-shrink: 0;
}

.sidebar-icon {
  font-size: 1.25rem;
  transition: transform 0.2s ease;
}

.sidebar-image {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  object-fit: contain;
}

.text-fallback {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  background-color: rgba(99, 102, 241, 0.12);
  color: var(--ab-main-color, #4f46e5);
  font-weight: 700;
  font-size: 0.85rem;
  border-radius: 6px;
}

.chevron-icon {
  transition: all 0.3s ease;
  &.rotate {
    transform: rotate(180deg);
  }
}
</style>
