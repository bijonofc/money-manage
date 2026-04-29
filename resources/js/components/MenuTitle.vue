<template>
 <span class="icon-wrapper" v-tooltip="appsbdUtls.translateGettext(menu.title)">
                  <template  v-if="menu.has_icon && menu.menu_icon">
                    <i :class="['sidebar-icon', menu.menu_icon]"></i>
                  </template>
                  <template v-else-if="isImage(menu.menu_icon)">
                    <img :src="menu.menu_icon" alt="icon" class="sidebar-image" />
                  </template>
                  <template v-else>
                    <span class="text-fallback">{{ firstLetter }}</span>
                  </template>
                </span>
    <span class="link_name ms-2 flex-grow-1"  v-translate>{{ menu.title }}</span>
    <span v-if="menu.children?.length && !sidebarClosed" class="chevron-icon" :class="{ rotate: isOpen }">
                  <i class="apb apb-chevron-down arrow"></i>
                </span>
</template>
<script setup>
import { computed } from 'vue'
import appsbdUtls from "@/libs/AppsbdUtls.js";

const props=defineProps({
    menu: {
        default: {}
    },
    isOpen:Boolean,
    sidebarClosed: Boolean
})
const firstLetter = computed(() =>
    props.menu.title ? props.menu.title.charAt(0).toUpperCase() : '?'
)


function isImage(icon) {
    return typeof icon === 'string' && (icon.startsWith('http') || icon.startsWith('/'))
}
</script>
<style scoped lang="scss">
.icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
}
.sidebar-icon {
    font-size: 1.5em;
  //  color: var(--ab-link-color);
}
.sidebar-image {
    width: 32px;
    height: 32px;
    border-radius: 5px;
    object-fit: contain;
}
.text-fallback {
    display: flex;
    align-items: center;
    justify-content: center;
    width: inherit;
    height: inherit;
    background-color: #ccc;
    color: #333;
    font-weight: bold;
    font-size: 1em;
    border-radius: 5px;
}
.chevron-icon {
    transition:all 700ms ease;
    &.rotate {
        transform: rotate(180deg);
    }
}
</style>
