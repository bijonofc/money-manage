<template>
    <div class="reg-body">
        <Header />

        <div class="reg-content">
            <div class="container-fluid h-100">
                <div class="row">
                    <div class="col p-0">
                       <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import Header from "@/modules/ClientRegistration/Header.vue";
</script>



<style scoped lang="scss">
.reg-content {
    overflow: auto;
    height: calc(100dvh - 80px);
}

@media (max-width: 768px) {
    .reg-content {
        height: calc(100dvh - 180px);
    }
}
</style>
