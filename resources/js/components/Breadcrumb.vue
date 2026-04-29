<template>
    <nav aria-label="breadcrumb" class="breadcrumb-container d-flex align-items-center">
        <ol class="breadcrumb d-flex mb-0">
            <li
                v-for="(crumb, idx) in crumbs"
                :key="idx"
                class="breadcrumb-item"
                :aria-current="idx === crumbs.length - 1 ? 'page' : null"
            >
                <router-link
                    v-if="crumb.to"
                    class="breadcrumb-link"
                    :to="crumb.to"
                >{{ crumb.text }}</router-link>
                <span v-else class="breadcrumb-current">{{ crumb.text }}</span>
            </li>
        </ol>
    </nav>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

const crumbs = computed(() => {
    return route.matched
        .filter(r => r.meta?.title)
        .map((r, i, arr) => ({
            text: typeof r.meta.title === 'function'
                ? r.meta.title(route)
                : r.meta.title,
            to: i < arr.length - 1
                ? { name: r.name, params: route.params }
                : null
        }));
});
</script>

<style scoped>
.breadcrumb-container {
    background: var(--ab-card-bg);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(116, 51, 51, 0.06);
    border-radius: 4px;
    margin-bottom: 1.5rem;
}
.breadcrumb {
    list-style: none;
    padding: 0;
    margin: 0;
}
.breadcrumb-item + .breadcrumb-item::before {
    content: '/';
    padding: 0 0.5rem;
    color: #9e9e9e;
}
.breadcrumb-link {
    color: #007bff;
    text-decoration: none;
    transition: color 0.2s ease;
}
.breadcrumb-link:hover {
    color: #0056b3;
}
.breadcrumb-current {
    color: var(--ab-body-color);
    font-weight: 600;
    font-size: 1.2rem;
}
</style>
