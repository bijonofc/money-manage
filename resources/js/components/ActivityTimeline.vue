<template>
    <div class="timeline-item">
        <!-- Vertical line -->
        <div class="timeline-line"></div>

        <!-- Timeline dot -->
        <div class="timeline-dot"></div>
    </div>
</template>

<script setup>
// No logic needed for static timeline
</script>

<style scoped>
.timeline-item {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px; /* space between dot and content if added later */
}

/* Vertical line behind the dot */
.timeline-line {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb; /* light gray line */
    left: 50%;
    transform: translateX(-50%);
    z-index: 0;
}

/* Timeline dot */
.timeline-dot {
    width: 12px;
    height: 12px;
    background: linear-gradient(135deg, #0d6efd, #3b82f6);
    border-radius: 50%;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(13, 110, 253, 0.3), 0 0 0 2px rgba(13, 110, 253, 0.2);
    transition: transform 0.2s ease;
    z-index: 1; /* keep dot above the line */
}

/* Optional hover effect */
.timeline-dot:hover {
    transform: scale(1.3);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.35), 0 0 0 2px rgba(13, 110, 253, 0.25);
}
</style>
