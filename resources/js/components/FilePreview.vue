<template>
    <div class="d-flex align-items-center gap-3" :class="props.styleClass">
        <div class="" v-for="(file, index) in files" :key="file.id || file.name || index">
            <FileCard :file="file" :file-url="getFileUrl(file)" :file-name="file.file_name" :removable="removable" @remove="$emit('remove', file, index)" />
        </div>
    </div>
</template>

<script setup>
import FileCard from "./FileCard.vue"

const props = defineProps({
    files: { type: Array, default: () => [] },
    removable: { type: Boolean, default: true },
    styleClass:{type:String,default:false}
})

const emit = defineEmits(["remove"])

function getFileUrl(file) {

    if (file instanceof File) return URL.createObjectURL(file)
    return file.file_path_full || file.url || file.file_path || file;
}
</script>
