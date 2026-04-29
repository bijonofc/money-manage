<template>
    <label class="me-3 form-label" v-translate>gbl.due.date</label>
    <div class="input-group">

        <!-- Month select only for Yearly -->
        <select v-if="recType === 'Y'" v-model="month" class="form-select form-select-sm">
            <option v-for="(m, idx) in months" :key="idx" :value="idx + 1">{{ m }}</option>
        </select>

        <!-- Day select -->
        <select v-model="selectedDay" class="form-select form-select-sm">
            <option v-for="d in daysInSelectedMonth" :key="d" :value="d">{{ d }}</option>
        </select>

    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
    modelValue: String,
    recVal: String,
    recType: { type: String, default: 'M' }
})

const emit = defineEmits(['update:modelValue'])

const today = new Date()
const currentYear = today.getFullYear()
const currentMonth = today.getMonth() + 1
const currentDay = today.getDate()

const months = [
    'January','February','March','April','May','June',
    'July','August','September','October','November','December'
]

const month = ref(currentMonth)
const selectedDay = ref(currentDay)

// Parse recVal
const recValDay = ref(currentDay)
const recValMonth = ref(currentMonth)

if (props.recVal) {
    if (props.recType === 'Y') {
        const [m, d] = props.recVal.split('-')
        recValMonth.value = Number(m)
        recValDay.value = Number(d)
        month.value = recValMonth.value
    } else {
        recValDay.value = Number(props.recVal)
        console.log(recValDay.value);
    }
}


if (props.modelValue) {
    if (props.recType === 'Y') {
        const [m, d] = props.modelValue.split('-')
        month.value = Number(m)
        selectedDay.value = Number(d)
    } else {
        selectedDay.value = Number(props.modelValue)
    }
} else {
    selectedDay.value = recValDay.value + 1

}
console.log(selectedDay.value);
const daysInSelectedMonth = computed(() => {
    const maxDay = props.recType === 'Y' ? new Date(currentYear, month.value, 0).getDate() : new Date(currentYear, currentMonth, 0).getDate()
    console.log(maxDay);

    let startDay
    if (props.modelValue) {
        startDay = selectedDay.value
    } else {
        startDay = recValDay.value + 1
        if (startDay > maxDay) startDay = maxDay
    }
    console.log(startDay);

    if (selectedDay.value < startDay) selectedDay.value = startDay
    if (selectedDay.value > maxDay) selectedDay.value = maxDay

    return Array.from({ length: maxDay - startDay + 1 }, (_, i) => i + startDay)
})
watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        if (props.recType === 'Y') {
            const [m, d] = newVal.split('-')
            month.value = Number(m)
            selectedDay.value = Number(d)
        } else {
            selectedDay.value = Number(newVal)
        }
    }
}, { immediate: true })

watch([() => props.recVal, () => props.recType], () => {
    // if (!props.modelValue) {
    //     selectedDay.value = recValDay.value + 1
    //     if (props.recType === 'Y') month.value = recValMonth.value
    // }

        selectedDay.value = recValDay.value + 1
        if (props.recType === 'Y') month.value = recValMonth.value

}, { immediate: true })


watch([month, selectedDay, () => props.recType], () => {
    if (props.recType === 'Y') {
        emit('update:modelValue', `${String(month.value).padStart(2, '0')}-${String(selectedDay.value).padStart(2, '0')}`)
    } else {
        emit('update:modelValue', String(selectedDay.value))
    }
}, { immediate: true })
</script>
