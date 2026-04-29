<template>
    <div ref="container" class="custom-scrollbar" :class="{ horizontal }" @scroll="handleScroll">
        <div ref="content" class="scroll-content">
            <slot />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, defineExpose } from 'vue'

const emit = defineEmits(['scroll', 'top', 'bottom', 'direction'])

const props = defineProps({
    horizontal: { type: Boolean, default: false },
    autoHideDelay: { type: Number, default: 1000 },
})

const container = ref(null)
const content = ref(null)
let hideTimeout = null
let lastScrollTop = 0
let resizeObserver = null


const handleScroll = (e) => {
    const el = e.target
    const scrollTop = el.scrollTop
    const scrollHeight = el.scrollHeight
    const clientHeight = el.clientHeight

    emit('scroll', e)

    const direction = scrollTop > lastScrollTop ? 'down' : 'up'
    emit('direction', direction)
    lastScrollTop = scrollTop

    if (scrollTop <= 0) emit('top')
    if (scrollTop + clientHeight >= scrollHeight) emit('bottom')

    el.classList.add('scrolling')
    clearTimeout(hideTimeout)
    hideTimeout = setTimeout(() => {
        el.classList.remove('scrolling')
    }, props.autoHideDelay)
}


const scrollTo = (y, behavior = 'smooth') => {
    container.value?.scrollTo({ top: y, behavior })
}

const scrollToBottom = (behavior = 'smooth') => {
    if (container.value) {
        container.value.scrollTo({
            top: container.value.scrollHeight,
            behavior,
        })
    }
}

defineExpose({ scrollTo, scrollToBottom })


onMounted(() => {
    nextTick(() => {
        if (window.ResizeObserver) {
            resizeObserver = new ResizeObserver(() => {
                container.value?.scrollTop
            })
            resizeObserver.observe(content.value)
        }
    })
})

onBeforeUnmount(() => {
    clearTimeout(hideTimeout)
    if (resizeObserver) resizeObserver.disconnect()
})
</script>

<style scoped>
.custom-scrollbar {
    position: relative;
    overflow-y: auto;
    overflow-x: hidden;
    max-height: 100%;
    scrollbar-width: thin; /* Firefox */
    scrollbar-color: var(--thumb-color) transparent;

    --thumb-color: #adb5bd; /* Bootstrap gray-500 */
    --thumb-hover: #6c757d; /* Bootstrap gray-600 */
    --track-bg: transparent;
}

/* Horizontal support */
.custom-scrollbar.horizontal {
    overflow-x: auto;
    overflow-y: hidden;
}

/* Chrome, Safari, Edge */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: var(--track-bg);
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: var(--thumb-color);
    border-radius: 8px;
    transition: background 0.3s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: var(--thumb-hover);
}

/* Auto-hide scrollbar thumb */
.custom-scrollbar:not(.scrolling)::-webkit-scrollbar-thumb {
    background-color: transparent;
    transition: background 0.5s ease-in-out;
}

.scroll-content {
    padding-right: 8px; /* Avoid overlap */
    padding-bottom: 8px;
}
</style>
