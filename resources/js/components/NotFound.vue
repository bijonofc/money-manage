<template>
    <div class="no-package-container d-flex flex-column justify-content-center align-items-center text-center">
        <div class="no-package-card shadow-sm">
            <h5 class="fw-bold text-danger mb-2">
                <i class="apb apb-exclamation me-2"></i>
                <span>{{ appsbdUtls.translateGettext('no.found', { name: 'package' }) }}</span>
            </h5>
            <p class="text-muted mb-3">
                <translate>gbl.select</translate>
            </p>
            <a
                href="https://nogorpos.com/"
                target="_blank"
                class="btn btn-danger px-4 py-2 fw-semibold"
            >
                <i class="apb apb-Home-01 me-2"></i>
                <translate>choose.pkg</translate>
            </a>
        </div>
    </div>
</template>

<script setup>
import appsbdUtls from "@/libs/AppsbdUtls.js";
</script>

<style scoped lang="scss">
.no-package-container {
    min-height: 60vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.no-package-card {
    background: var(--ab-card-bg);
    border: 1px solid #f8d7da;
    border-radius: 18px;
    padding: 2rem 2.5rem;
    max-width: 420px;
    width: 100%;
    box-shadow: 0 6px 20px rgba(255, 0, 0, 0.08);



    h5 {
        color: #dc3545;
        font-size: 1.3rem;
    }

    p {
        font-size: 0.95rem;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s ease;

        &:hover {
            background-color: #bb2d3b;
            transform: scale(1.03);
        }
    }
}
</style>

