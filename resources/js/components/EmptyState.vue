<template>
    <div class="text-center py-2">
        <div class="w-100 d-inline-flex flex-column align-items-center justify-content-center p-3 border border-light rounded-4 bg-light shadow-sm">
            <i class="apb apb-circle-information text-danger fs-1 mb-2"></i>
            <h5 class="text-danger fw-semibold mb-1">
                {{AppsbdUtls.translateGettext('gbl.not.found',{name:'feature'})}}

            </h5>
            <p class="text-muted mb-0">
                {{AppsbdUtls.translateGettext('gbl.not.found.description',{name:'feature'})}}

            </p>

        </div>
    </div>
</template>

<script setup>
import AppsbdUtls from '../libs/AppsbdUtls.js'

const props = defineProps({
    feature: {
        type: String,
        required: true
    }
})


</script>

<style scoped>
</style>

