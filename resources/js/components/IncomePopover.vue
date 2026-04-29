<template>
    <div class="p-3">
        <Form class="add-income" @submit="addIncome">
            <!-- Header -->
            <div class="form-header d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary fw-semibold">
                    <i class="apb apb-dollar-sign me-2"></i>
                    <span v-translate>gbl.add.income</span>
                </h6>
                <button type="button" class="btn-close btn-close-custom" aria-label="Close" @click="closePopover"></button>
            </div>


            <div class="my-3">
                <response-msg v-if="!isShowLoader" :message="msg" />
            </div>

            <div v-if="!showMsgOnly" :class="{ 'dimmed': isShowLoader }">
                <div class="row row-cols-1 g-3">

                    <div class="col">
                        <input-field label="gbl.amount" container-class="mb-0" v-model="income.amount" name="income.amount" rules="required|numeric|min_value:1" placeholder="0.00"/>
                    </div>

                    <div class="col">
                        <label class="form-label fw-medium text-muted">
                            <i class="apb apb-note me-1"></i>
                            <translate>gbl.note</translate>
                        </label>
                        <Field
                            as="textarea"
                            class="form-control form-control-sm rounded-2"
                            rows="3"
                            label="gbl.note"
                            rules="required"
                            v-model="income.note"
                            :placeholder="AppsbdUtls.translateGettext('gbl.note.placeholder')"
                            name="note"
                        />
                        <ErrorMessage name="note" class="appsbd-error" />
                    </div>
                </div>

                <!-- File Upload Section -->
                <div class="row mt-3">
                    <div class="col">
                        <div class="border rounded-3 p-3 bg-light-subtle">
                            <label class="form-label fw-medium text-muted  d-flex align-items-center mb-0">
                                <i class="apb apb-file-alt me-1"></i>
                                <span v-translate>gbl.files</span>
                                <small class="text-muted ms-2 fw-normal">({{ AppsbdUtls.translateGettext('gbl.optional') }})</small>
                            </label>

                            <image-uploader
                                v-model="income.files"
                                input-id="file_path"
                                accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt"
                                :max-files="5"
                                :max-size="5"
                            />

                            <file-preview
                                v-if="uploadedImages.length > 0"
                                class="mt-3"
                                :files="uploadedImages"
                                :removable="true"
                                @remove="$emit('remove', $event)"
                            />
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3">
                    <div class="col">
                        <div class="d-flex justify-content-end gap-2">
                            <slot name="actionButtons">
                                <button
                                    type="button"
                                    class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3"
                                    :disabled="isShowLoader"
                                    @click="closePopover"
                                >
                                    <i class="apb apb-close"></i>
                                    <span v-translate>gbl.cancel</span>
                                </button>
                                <button
                                    class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 shadow-sm"
                                    :disabled="isShowLoader"
                                    type="submit"
                                >
                                    <i v-if="!isShowLoader" class="apb apb-check-circle"></i>
                                    <SmallLoader v-if="isShowLoader" :show="isShowLoader" size="5" color="#FFF" />
                                    <span v-translate>gbl.save.income</span>
                                </button>
                            </slot>
                        </div>
                    </div>
                </div>
            </div>
        </Form>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Field, Form,ErrorMessage } from 'vee-validate'
import { hideAllPoppers } from 'floating-vue'
import AppsbdUtls from "@/libs/AppsbdUtls.js"
import SmallLoader from '@/components/SmallLoader.vue'
import ImageUploader from "@/components/ImageUploader.vue"
import FilePreview from "@/components/FilePreview.vue"
import { ResponseMsg, InputField } from '@appsbd/vue3-appsbd-libs'
import {useCompanyStore} from "@/modules/AdminPanel/Company/CompanyStore.js";
const companyStore=useCompanyStore();
const uploadedImages = ref([])

const props = defineProps({
    isDisabled: Boolean,
    companyId: { default: null },
    taskTitle: { default: null }
})

const emit = defineEmits(['add'])

const income = reactive({
    amount: '',
    files: [],
    note: ''
})

const isShowLoader = ref(false)
const msg = ref(null)
const showMsgOnly = ref(false)

const closePopover = () => hideAllPoppers()

async function addIncome() {
    try {
        isShowLoader.value = true
      const response=await companyStore.addIncome(income)
      if(response){
        emit('incomeAdded', response)
        closePopover()
      }
        // actual logic here
    } catch (e) {
        console.error('Failed to save income', e)
    }
    isShowLoader.value = false
}
</script>

<style scoped lang="scss">
.add-income {
    width: 450px;
    background: var(--ab-card-bg);
    border-radius: 16px;
    .form-control {
        border: 1px solid var(--ab-border-color);
        background: var(--ab-input-bg);
        transition: all 0.2s ease;

        &:focus {
            border-color: var(--ab-primary);
            background: var(--ab-input-bg);
            box-shadow: 0 0 0 3px rgba(var(--ab-primary-rgb, 28, 148, 255), 0.1);
        }
    }

    .form-label {
        font-size: 0.85rem;
        color: var(--ab-text-muted);
    }

    .input-group-text {
        background: var(--ab-light-bg);
        border-color: var(--ab-border-color);
        color: var(--ab-text-muted);
    }
}

.form-header {
    padding-bottom: 12px;
    border-bottom: 1px solid var(--ab-border-color);

    .btn-close-custom {
        opacity: 0.6;
        transition: opacity 0.2s;
        padding: 4px;
        background-size: 10px;

        &:hover {
            opacity: 1;
            background-color: var(--ab-light-bg);
        }
    }
}

.btn-primary {
    background: linear-gradient(135deg, var(--apbd-btn-bg-color, #1c94ff), var(--apbd-btn-bg-hover, #1488f5));
    border: none;
    font-weight: 500;

    &:hover:not(:disabled) {
        background: linear-gradient(135deg, var(--apbd-btn-bg-hover, #1488f5), #0d7ae0);
        transform: translateY(-1px);
    }

    &:disabled {
        opacity: 0.6;
    }
}

.btn-outline-secondary {
    border-color: var(--ab-border-color);

    &:hover:not(:disabled) {
        background-color: var(--ab-light-bg);
    }
}

.dimmed {
    opacity: 0.5;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.bg-light-subtle {
    background-color: rgba(var(--ab-light-bg-rgb, 248, 249, 250), 0.5) !important;
}

// Animation for loader
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.add-income {
    animation: fadeIn 0.3s ease;
}
</style>
