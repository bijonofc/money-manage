<template>
    <div>
        <div class="row row-cols-1 g-3">

            <!-- Amount -->
            <div class="col">
                <input-field
                    label="gbl.amount"
                    container-class="mb-0"
                    v-model="expenseProxy.amount"
                    name="expense.amount"
                    rules="required|numeric|min_value:1"
                    placeholder="0.00"
                />
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
                    rules="required"
                    name="note"
                    v-model="expenseProxy.note"
                    :placeholder="AppsbdUtls.translateGettext('gbl.note.placeholder')"
                />
                <ErrorMessage name="note" class="appsbd-error" />
            </div>
        </div>

        <!-- Files -->
        <div class="row mt-3">
            <div class="col">
                <div class="border rounded-3 p-3 bg-light-subtle">
                    <label class="form-label fw-medium text-muted d-flex align-items-center mb-0">
                        <i class="apb apb-file-alt me-1"></i>
                        <span v-translate>gbl.files</span>
                        <small class="text-muted ms-2 fw-normal">
                            ({{ AppsbdUtls.translateGettext('gbl.optional') }})
                        </small>
                    </label>

                    <image-uploader
                        v-model="expenseProxy.files"
                        input-id="file_path"
                        accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt"
                        :max-files="5"
                        :max-size="5"
                    />

                    <file-preview
                        v-if="uploadedImages.length"
                        class="mt-3"
                        :files="uploadedImages"
                        removable
                        @remove="$emit('remove', $event)"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Field, ErrorMessage } from 'vee-validate'
import AppsbdUtls from '@/libs/AppsbdUtls.js'

import ImageUploader from '@/components/ImageUploader.vue'
import FilePreview from '@/components/FilePreview.vue'
import { InputField } from '@appsbd/vue3-appsbd-libs'

const props = defineProps({
    expense: {
        type: Object,
        required: true
    },
    uploadedImages: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['update:expense', 'remove'])


const expenseProxy = computed({
    get: () => props.expense,
    set: (val) => emit('update:expense', val)
})
</script>


