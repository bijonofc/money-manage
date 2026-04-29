<template>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <translate>contact.details</translate>
            </h5>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <input-field
                        label="gbl.contact.name"
                        class="form-control"
                        container-class="mb-0"
                        v-model="localData.contact_name"
                        name="contact_name"
                        rules="required"
                    />
                </div>

                <div class="col-12 col-md-6">
                    <input-field
                        label="gbl.email"
                        class="form-control"
                        container-class="mb-0"
                        v-model="localData.email"
                        name="email"
                        rules="email"
                    />
                </div>

                <div class="col-12 col-md-6">
                    <contact-number-input
                        name="phone"
                        rules="required"
                        v-model="localData.phone"
                        label="gbl.contact.no"
                    />
                </div>

                <div class="col-12 col-md-6">
                    <apbd-switch-button
                        v-model="localData.is_whatsapp"
                        id="is_whatsapp"
                        container-class="form-switch-md"
                    >
                        <template #topLabel>
                            <translate>is.whatsapp</translate>
                        </template>
                    </apbd-switch-button>
                </div>

            </div>
        </div>
    </div>


    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">
                <translate>contact.company</translate>
            </h5>
        <div class="row g-3">
            <div class="col-12">
                <input-field
                    label="gbl.comp.name"
                    class="form-control"
                    container-class="mb-0"
                    v-model="localData.company_name"
                    name="company_name"
                    rules=""
                />
            </div>


            <div class="col-12 col-md-6">
                <contact-number-input
                    name="company_phone"
                    rules=""
                    v-model="localData.company_phone"
                    label="gbl.comp.contact.no"
                />
            </div>


            <div class="col-12 col-md-6">
                <input-field
                    label="gbl.comp.email"
                    class="form-control"
                    container-class="mb-0"
                    v-model="localData.company_email"
                    name="company_email"
                    rules="email"
                />
            </div>


        </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, watch } from 'vue'
import ContactNumberInput from '@/components/ContactNumberInput.vue'
import {InputField, ApbdSwitchButton} from '@appsbd/vue3-appsbd-libs'

const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['update:modelValue'])

const localData = reactive({ ...props.modelValue })

watch(
    () => localData,
    (val) => {
        emit('update:modelValue', val)
    },
    { deep: true }
)

watch(
    () => props.modelValue,
    (val) => {
        Object.assign(localData, val)
    },
    { deep: true }
)
</script>
