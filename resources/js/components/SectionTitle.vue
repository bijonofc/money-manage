<template>
    <div class="d-flex align-items-center gap-2 mb-4 mt-4">
        <i :class="['apb', icon, 'section-icon']"></i>
        <h4 class="section-title mb-0" v-translate>{{ title }}</h4>
    </div>
</template>

<script setup>
defineProps({
    icon: String,
    title: String
});
</script>

<style scoped>
.section-icon {
    font-size: 22px;
    color: #4e73df;
}
.section-title {
    font-size: 20px;
    font-weight: 600;
}
</style>
