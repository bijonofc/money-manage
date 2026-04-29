<template>
    <label class="me-3 form-label" v-translate>recurring.type</label>
    <div class="input-group">
        <select :value="props.rec_val.recType" @change="e => updateRecVal('recType', e.target.value)"   class="form-select form-select-sm">
            <option value="D" v-translate>gbl.daily</option>
            <option value="M" v-translate>gbl.monthly</option>
            <option value="Y" v-translate>gbl.yearly</option>
        </select>
        <select v-if="props.rec_val.recType === 'Y'" :value="props.rec_val.month"  @change="e => updateRecVal('month', e.target.value)" class="form-select form-select-sm">
            <option v-for="(m, idx) in months" :key="idx" :value="idx + 1">{{ m }}</option>
        </select>
        <select v-if="props.rec_val.recType === 'M' || props.rec_val.recType === 'Y'" :value="props.rec_val.day"  @change="e => updateRecVal('day', e.target.value)" class="form-select form-select-sm">
            <option v-for="d in daysInSelectedMonth" :key="d" :value="d">{{ d }}</option>
        </select>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
const currentDate = new Date()
const currentYear = currentDate.getFullYear()
const currentMonth = currentDate.getMonth() + 1
const currentDay = currentDate.getDate()
const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
const daysInSelectedMonth = computed(() => {return new Date(currentYear, currentMonth, 0).getDate()})
let props = defineProps({
    rec_val: {
        type: Object,
        default: () => ({ recType: 'M', month: 1, day: 1 })
    }
})
const emit = defineEmits(['update:rec_val'])
const updateRecVal = (key, value) => {
    emit('update:rec_val', { ...props.rec_val, [key]: value })
}


</script>
