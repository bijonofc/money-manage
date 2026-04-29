<template>
    <div class="chat-input-wrapper">
        <div v-if="comment.attachments.length" class="file-preview-above">
            <FileCard v-for="(file, index) in comment.attachments" :key="file.id || file.name || index" :file="file" :file-url="getFileUrl(file)" :removable="true" @remove="removeAttachment(index)" />
        </div>
        <div class="chat-input">
            <label class="chat-attach" title="Attach file">
                <i class="apb apb-paperclip"></i>
                <input type="file" class="d-none" multiple @change="handleFileUpload" />
            </label>
            <input v-model="comment.message" type="text" :placeholder="placeholder" class="chat-input-field" @keydown.enter.prevent="handleSend" />

            <span @click="handleSend" class="chat-send-btn">
                <i v-if="!props.loading" class="apb apb-send-01"></i>
                <SmallLoader v-if="props.loading"  :show="props.loading" color="#ece" size="5" />
            </span>
        </div>
    </div>
</template>

<script setup>
import { reactive, defineEmits, defineProps } from 'vue'
import FileCard from './FileCard.vue'
import SmallLoader from '@/components/SmallLoader.vue'

const props = defineProps({
    placeholder: { type: String, default: 'Write a message...' },
    loading: { type: Boolean, default: false }
})

const emit = defineEmits(['send'])

const comment = reactive({
    message: '',
    attachments: []
})

// Handle file upload
function handleFileUpload(e) {
    const files = Array.from(e.target.files)
    comment.attachments.push(...files)
    e.target.value = null
}
function removeAttachment(index) {
    comment.attachments.splice(index, 1)
}

// Send message + attachments
function handleSend() {
    if (!comment.message && comment.attachments.length === 0) return

    emit('send', { message: comment.message, attachments: [...comment.attachments] })

    // Clean up local previews
    comment.attachments.forEach(file => {
        if (file instanceof File && file.previewUrl) {
            URL.revokeObjectURL(file.previewUrl)
        }
    })

    comment.message = ''
    comment.attachments = []
}

// Generate file preview URL
function getFileUrl(file) {
    if (file instanceof File) {
        if (!file.previewUrl) {
            file.previewUrl = URL.createObjectURL(file)
        }
        return file.previewUrl
    }
    return file.file_path_full || file.url || file
}
</script>

<style scoped>
.chat-input-wrapper {
    display: flex;
    flex-direction: column;
    gap: 6px;
}


.file-preview-above {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}


.chat-input {
    display: flex;
    border: 1px solid #e9ecef;
    align-items: center;
    width: 100%;
    padding: 8px 12px;
    background-color: #ffffff;
    border-radius: 999px;
    gap: 8px;
}

/* File attach icon */
.chat-attach {
    display: flex;
    width: 30px;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #6c757d;
    height: 30px;
    font-size: 15px;
    border-radius: 50%;
    flex-shrink: 0;
}

.chat-attach:hover {
    background: #bfbfbf;
}

/* Text input */
.chat-input-field {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-size: 0.95rem;
    color: #212529;
    padding: 6px 0;
    min-width: 50px;
}

.chat-input-field::placeholder {
    color: #6c757d;
}

/* Send button */
.chat-send-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
    font-size: 15px;
    flex-shrink: 0;
}

.chat-send-btn:hover {
   background: #bfbfbf;
}
</style>
