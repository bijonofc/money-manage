<template>
    <li v-if="hasPermission"  class="sidebar-item" :class="{ showmenu: isOpen }">
        <div  class="icons-link pointer" @click="handleToggle">
            <div v-if="menu.children" class="ab-menu-a d-flex justify-between align-items-center w-100" :class="{'ab-active':is_active(menu)}">
                <menu-title  :is-open="isOpen" :sidebar-closed="sidebarClosed" :menu="menu"/>
            </div>
            <router-link v-else :to="menu.route||''" class="ab-menu-a d-flex justify-between align-items-center w-100">
                <menu-title  :is-open="isOpen" :sidebar-closed="sidebarClosed" :menu="menu"/>
            </router-link>
            <div v-if="menu.children?.length && props.sidebarClosed" class="hover-sub-menu">
                <ul>
                    <li v-for="child in filteredChildren" :key="child.id">
                        <router-link :to="child.route || '#'" class="d-flex align-items-center">
                            <span class="icon-wrapper">
                              <template v-if="child.has_icon && child.menu_icon">
                                <i :class="['sidebar-icon', child.menu_icon]"></i>
                              </template>
                              <template v-else-if="isImage(child.menu_icon)">
                                <img :src="child.menu_icon" alt="icon" class="sidebar-image" />
                              </template>
                              <template v-else>
                                <span class="text-fallback">
                                  {{ child.title?.charAt(0).toUpperCase() || '?' }}
                                </span>
                              </template>
                            </span>
                            <span class="link-name ms-2" v-translate>{{ child.title }}</span>
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Regular Submenu -->
        <ul v-if="filteredChildren.length && isOpen" class="sub-menu p-0">
            <SidebarItem
                v-for="child in filteredChildren"
                :key="child.id"
                :menu="child"
                :is-open="false"
                @toggle="$emit('toggle', $event)"
            />
        </ul>
    </li>
</template>

<script setup>
import { computed, onMounted, ref,getCurrentInstance } from 'vue';
const { proxy } = getCurrentInstance();
import { useDashboardStore } from '@/modules/AdminPanel/Dashboard/DashboardStore.js';
const dashboardStore=useDashboardStore();
import { useRoute } from 'vue-router'
import MenuTitle from '@/components/MenuTitle.vue'
import Acl from "@/libs/acl.js";
const route = useRoute();
const props = defineProps({
    menu: Object,
    sidebarClosed: Boolean
})

const hasPermission = computed(() => {
    if (props.menu.acl) {
        return Acl.checkACL(props.menu.acl);
    }
    return true;
});

const emit = defineEmits(['toggle'])
const filteredChildren = computed(() => {
    if (!props.menu.children) return [];
    return props.menu.children.filter(child => {
        return !child.acl || proxy.$CheckACL(child.acl);
    });
});
const firstLetter = computed(() =>
    props.menu.title ? props.menu.title.charAt(0).toUpperCase() : '?'
)

const isToggled=ref(null);
const isRouter=ref(null);
const isOpen=computed(()=>{
    if(isToggled.value==null){
        return route.path.startsWith(props.menu.route);
    }
    return isToggled.value;
});


onMounted(()=>{


})



function handleToggle() {
    isToggled.value=!isOpen.value;
}

function isImage(icon) {
    return typeof icon === 'string' && (icon.startsWith('http') || icon.startsWith('/'))
}
function is_active(menu) {
    const route=useRoute();
    return route.path.startsWith(menu.route);
}
</script>

<style scoped lang="scss">

.link-name {
    font-size: 14px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.chevron-icon {
    font-size: 16px;
    transition: transform 0.3s ease;
    display: flex;
    align-items: center;
}


/* Hover submenu panel */
.hover-sub-menu {
    display: none;
    position: absolute;
    left: 100%;
    top: 0;
    background-color: var(--ab-sidebar-bg);
    border: 1px solid rgba(207, 207, 215, 0.14);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    z-index: 100;
    min-width: 200px;
    white-space: nowrap;
    padding: 0.3rem 0;
    border-radius: 5px;

    ul {
        list-style: none;
        margin: 0;
        padding: 0;

        li {
            a {
                display: flex;
                align-items: center;
                padding: 0.75rem 1.25rem;
                color: var(--ab-link-color);
                text-decoration: none;
                font-size: 14px;
                font-weight: 600;
                transition: background-color 0.3s ease;

                &:hover {
                    background-color: var(--ab-hover-bg, rgba(233, 233, 233, 0.28));
                }

                .icon-wrapper {
                    margin-right: 0.5rem;
                    font-size: 18px;
                }

                .link_name {
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
            }
        }
    }
}

</style>
