<template>
    <div>
        <label class="form-label" v-if="props.label">{{appsbdUtls.translateGetMsg(label)}}</label>
        <Field :name="name" :label="label" v-model="formValue" :rules="rules" v-slot="{ field }">
            <input
                v-bind="field"
                :value="formValue"
                v-model="formValue"
                class="form-control"
                :class="$attrs?.class?$attrs?.class:''"
                :placeholder="placeholder"
                @focus="initBDPhone"
                @change="(e) => { onChange(e); field.onChange(e); }"
                @input="(e) => { formatBDPhone(e); field.onInput(e); }"
            />
        </Field>
        <ErrorMessage :name="name" class="apbd-v-error" />
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Field, ErrorMessage } from 'vee-validate'
import appsbdUtls from "../libs/AppsbdUtls.js";

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    name: {
        type: String,
        default: 'contact_no',
    },
    label: {
        type: String,
        default: '',
    },
    rules: {
        type: String,
        default: 'required',
    },
    placeholder: {
        type: String,
        default: '+880 1XXX-XXXXXX',
    },
})

const emit = defineEmits(['update:modelValue'])

const formValue = ref(props.modelValue)

watch(
    () => props.modelValue,
    (newVal) => {
        formValue.value = newVal
    }
)

function initBDPhone(e) {
    if (!formValue.value || !formValue.value.startsWith('+880')) {
        formValue.value = '+880 '
        emit('update:modelValue', formValue.value)
    }

    if (!e.target.value.startsWith('+880 ')) {
        e.target.value = '+880 '
    }
}

function onChange(e) {
    let val = e.target.value
    if (!val.startsWith('+880')) {
        val = '+880 '
    }

    let digits = val.replace(/\D/g, '').slice(3, 13) // 10 digits max
    if (digits.length < 10) {
        e.target.value = ''
        formValue.value = ''
        emit('update:modelValue', '')
    }
}

function formatBDPhone(e) {
    let val = e.target.value

    if (!val.startsWith('+880 ')) {
        val = '+880 '
    }

    // Remove all non-digits after prefix
    let digits = val.replace(/\D/g, '').slice(3, 13)

    // Remove starting 0 if exists
    if (digits.startsWith('0')) {
        digits = digits.slice(1)
    }

    let formatted = '+880 '
    if (digits.length > 0) formatted += digits.substring(0, 4)
    if (digits.length > 4) formatted += '-' + digits.substring(4, 10)

    formValue.value = formatted
    e.target.value = formatted
    emit('update:modelValue', formatted)
}
</script>
