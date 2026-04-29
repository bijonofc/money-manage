<template>
    <div class="file-card position-relative text-center" @click="handleClick">
        <template v-if="isImage">
            <img :src="fileUrl" class="file-thumb" />
        </template>
        <template v-else>
            <i :class="getFileIconClass(file)" class="file-icon"></i>
        </template>

        <span v-if="removable" class="remove-btn" @click.stop="$emit('remove', file)">
      <i class="apb apb-x-circle"></i>
    </span>

        <div class="file-tooltip">
            {{ getFileName(fileUrl,fileName) }}
        </div>

        <!-- Image Preview Modal -->
        <div v-if="showPreview" class="preview-overlay" @click.stop="closePreview">
            <div class="preview-container" @click.stop>
                <span class="close-btn" @click="closePreview">
                    <i class="apb apb-x-circle"></i>
                </span>
                <img :src="fileUrl" class="preview-image" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    file: { type: [Object, String], required: true },
    fileUrl: { type: String, required: true },
    fileName: { type: String, required: false },
    removable: { type: Boolean, default: true },
})

const showPreview = ref(false)

const isImage = computed(() => {
    if (props.file instanceof File) return props.file.type.startsWith('image/')
    return /\.(jpg|jpeg|png|gif|webp)$/i.test(props.fileUrl)
})

function getFileIconClass(file) {
    const ext = getFileName(file?.file_path_full || file?.name || file.file_url || file).split(".").pop().toLowerCase()
    switch (ext) {
        case "pdf": return "apb apb-pdf-3 text-danger"
        case "doc": case "docx": return "apb apb-file-word text-primary"
        case "xls": case "xlsx": return "apb apb-file-excel text-success"
        default: return "apb apb-file"
    }
}

function getFileName(path, fileName) {
    // যদি fileName থাকে, সেটাই return করো
    if (fileName) return fileName;

    // path না থাকলে empty string return
    if (!path || typeof path !== 'string') return "";

    // path থেকে last part return করো
    const parts = path.split('/');
    return parts.length ? parts[parts.length - 1] : '';
}

function handleClick() {
    if (isImage.value) {
        showPreview.value = true
    } else {
        window.open(props.fileUrl, "_blank")
    }
}

function closePreview() {

    showPreview.value = false
}
</script>

<style lang="scss" scoped>
.file-card {
    width: 54px;
    height: 46px;
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 6px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    display: flex;
    justify-content: center;
    align-items: center;
    transition: 0.2s;
    position: relative;
    cursor: pointer;

    &:hover {
        .file-tooltip {
            display: block;
        }
    }

    .file-thumb {
        max-width: 40px;
        max-height: 32px;
        object-fit: cover;
        border-radius: 6px;
    }

    .file-icon {
        font-size: 28px;
    }

    .file-tooltip {
        display: none;
        position: absolute;
        bottom: -26px;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        background: #333;
        color: #fff;
        padding: 3px 6px;
        font-size: 11px;
        border-radius: 4px;
        z-index: 10;
    }

    .remove-btn {
        position: absolute;
        top: -5px;
        right: -8px;
        cursor: pointer;
        color: #d9534f;
        background: #fff;
        border-radius: 50%;
        padding: 2px;
        font-size: 13px;
    }
}

/* Preview Modal */
.preview-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;

    .preview-container {
        position: relative;

        .preview-image {
            max-width: 90vw;
            max-height: 90vh;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }

        .close-btn {
            position: absolute;
            font-size: 16px;
            color: #fff;
            top: -2px;
            right: -1px;
            border: none;
            cursor: pointer;
            line-height: 28px;
            text-align: center;
            padding: 0;
            z-index: 100;
            &:hover{
                color: red;
            }
        }
    }
}
</style>

