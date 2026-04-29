// useResponsive.js
import { computed, onMounted, onUnmounted, ref } from "vue";

export default function useBreakpoint() {
    const windowWidth = ref(window.innerWidth);

    const onWidthChange = () => {
        windowWidth.value = window.innerWidth;
    };

    onMounted(() => {
        window.addEventListener('resize', onWidthChange);
    });

    onUnmounted(() => {
        window.removeEventListener('resize', onWidthChange);
    });

    const ScreenType = computed(() => {
        if (windowWidth.value < 576) return 'xs';
        if (windowWidth.value < 768) return 'sm';
        if (windowWidth.value < 992) return 'md';
        if (windowWidth.value < 1200) return 'lg';
        if (windowWidth.value < 1920) return 'xl';
        return 'xxl';
    });

    const ScreenWidth = computed(() => windowWidth.value);

    const isUptoTab = computed(() => {
        const size = vitePos?.m_size || 768 ;
        return ScreenType.value === 'xs' ||
            ScreenType.value === 'sm' ||
            (size > 0 && ScreenWidth.value <= size);
    });

    return { ScreenWidth, ScreenType, isUptoTab };
}
