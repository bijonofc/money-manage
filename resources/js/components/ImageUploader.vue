<template>
    <div>
        <FilePreview style-class="mb-3" :files="modelValue" :removable="true" @remove="removeFile" />

        <input type="file" :id="inputId" :accept="accept" multiple hidden @change="handleFileChange" />
        <label :for="inputId" class="btn btn-outline-primary">
            <i class="apb apb-upload"></i> <translate>upload.files</translate>
        </label>

    </div>
</template>

<script setup>
import FilePreview from "./FilePreview.vue"

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    inputId: { type: String, required: true },
    accept: { type: String, default: "*" }
})

const emit = defineEmits(["update:modelValue"])

function handleFileChange(event) {
    const files = Array.from(event.target.files)
    emit("update:modelValue", [...props.modelValue, ...files])
    event.target.value = null
}

function removeFile(file) {
    const updated = props.modelValue.filter(f => f !== file)
    emit("update:modelValue", updated)
}
</script>
