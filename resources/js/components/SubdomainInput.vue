<template>
    <div class="subdomain-inline-edit w-100">

        <!-- DISPLAY MODE -->
        <div v-if="!editing" class="display-row">
            <span class="display-text">
                {{ displayValue }}
            </span>

            <i
                v-if="!disabled"
                class="apb apb-pen-square edit-icon text-primary"
                title="Edit"
                @click="startEdit"
            ></i>
        </div>

        <!-- EDIT MODE -->
        <div v-else class="edit-wrapper">
            <div class="input-group input-group-sm w-100">
                <input
                    type="text"
                    class="form-control"
                    v-model="internalValue"
                    :placeholder="placeholder"
                    @input="onInput"
                    autofocus
                />

                <span class="input-group-text domain-suffix">
                    .{{ mainDomain }}
                </span>

                <button
                    class="input-group-text action-btn"
                    :disabled="isChecking || !internalValue"
                    @click="saveSubDomain"
                >
                    <Rolling v-if="isChecking" width="16" height="16" />
                    <i v-else class="apb apb-circle-check text-success"></i>
                </button>

                <button
                    class="input-group-text action-btn"
                    @click="cancelEdit"
                >
                    <i class="apb apb-x-circle text-danger"></i>
                </button>
            </div>

            <p v-if="!isValid && errorMsg" class="text-danger small mt-1">
                {{ errorMsg }}
            </p>
        </div>

    </div>
</template>


<script setup>
import { ref, watch, computed } from 'vue'
import Rolling from '@/components/Rolling.vue'
import { useCustomerStore } from '@/modules/AdminPanel/Customer/CustomerStore.js'
import AppsbdUtls from '@/libs/AppsbdUtls.js'

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Enter subdomain' },
    mainDomain: { type: String, required: true },
    disabled: { type: Boolean, default: false },
    customerId: { type: Number, required: true },
})

const emit = defineEmits(['update:modelValue', 'saved', 'edit-start', 'edit-end'])

const customerStore = useCustomerStore()

const editing = ref(false)
const internalValue = ref(props.modelValue)
const isChecking = ref(false)
const isValid = ref(true)
const errorMsg = ref('')
let timer = null

watch(() => props.modelValue, val => {
    internalValue.value = val
})

const displayValue = computed(() => {
    return props.modelValue
        ? `${props.modelValue}.${props.mainDomain}`
        : 'N/A'
})

const startEdit = () => {
    if (props.disabled) return
    editing.value = true
    internalValue.value = props.modelValue
    emit('edit-start')
}

const cancelEdit = () => {
    editing.value = false
    isValid.value = true
    errorMsg.value = ''
    internalValue.value = props.modelValue
    emit('edit-end')
}

const onInput = () => {
    if (timer) clearTimeout(timer)

    isChecking.value = true

    timer = setTimeout(async () => {
        try {
            const response = await customerStore.getIsValidDomain({
                sub_domain: internalValue.value,
                id: props.customerId,
            })

            if (!response.status) {
                isValid.value = false
                errorMsg.value = response?.msg?.error?.[0] || 'Invalid domain'
            } else {
                isValid.value = true
                errorMsg.value = ''
            }
        } finally {
            isChecking.value = false
        }
    }, 500)
}

const saveSubDomain = async () => {
    if (!internalValue.value || !isValid.value) return

    isChecking.value = true

    try {
        const response = await customerStore.updateCustomer({
            id: props.customerId,
            field: 'sub_domain',
            value: internalValue.value,
        })

        if (response.status) {
            emit('update:modelValue', internalValue.value)
            emit('saved', true)
            editing.value = false
            emit('edit-end')
        }

        AppsbdUtls.ShowServerResponseNotification(response.msg, 5000)
    } catch (e) {
        AppsbdUtls.ShowServerResponseNotification({ errors: [e.message] }, 5000)
    } finally {
        isChecking.value = false
    }
}
</script>


<style scoped lang="scss">
.subdomain-inline-edit {
    width: 100%;
    .input-group{
        height: 33px !important;
    }
    .input-group-text{
    height: unset !important;
   }
}

/* DISPLAY MODE */
.display-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
}

.display-text {
    font-weight: 600;
    word-break: break-word;
}

.edit-icon {
    cursor: pointer;
    font-size: 1rem;
}

/* INPUT */
.input-group {
    width: 100%;
}

.domain-suffix {
    white-space: nowrap;
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

/* MOBILE */
@media (max-width: 576px) {
    .display-row {
        //flex-direction: column;
        align-items: flex-start;
        gap: .25rem;
    }

    .edit-icon {
        align-self: flex-end;
    }
}
</style>

