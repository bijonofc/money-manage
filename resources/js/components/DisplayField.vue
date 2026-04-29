<template>
    <div class="d-flex align-items-start justify-content-between py-2">
        <label class="fw-medium me-2 text-secondary">
            <translate>{{ label }}</translate>:
        </label>

        <div class="fw-semibold text-dark text-end text-break">
            {{ value || 'N/A' }}
        </div>
    </div>
</template>

<script setup>
defineProps({
    label: String,
    value: [String, Number, Boolean]
});
</script>

<style scoped>
div {
    font-size: 0.95rem;
}
</style>
