<template>
 <span class="fw-semibold" :class="ledgerType=='C'?'text-success':'text-danger'">
    {{ledgerType==='C'?'+':'-'}}{{AppHelper.formatCurrency(amount)}}
 </span>
</template>

<script setup>
import AppHelper from "@/libs/AppHelper.js";
const props = defineProps({
    ledgerType: { type: String, required: true },
    amount: { type: Number, required: true }
})

</script>
<style scoped lang="scss">

</style>
