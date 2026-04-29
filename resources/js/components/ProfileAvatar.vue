<template>
    <ResponseMsg :message="img_msg" />

    <div class="avatar-wrapper">
        <img :src="previewImage || user.image_url || defaultImage" class="profile-img"/>
        <div  class="avatar-overlay" @click="triggerFileInput">
            <i class="apb apb-camera fs-5"></i>
        </div>
    </div>

    <h6 class="mt-3 fw-semibold">{{ user.name }}</h6>
    <p class="text-muted small">{{ user.email }}</p>
    <input ref="fileInput" type="file" class="d-none" accept="image/*" @change="handleFileChange"/>

    <button  class="btn btn-outline-primary btn-sm mt-2 mx-auto d-flex align-items-center gap-2" :disabled="!selectedFile || loading" @click="submit">
        <i class="apb apb-upload"></i>
        <translate>upload.image</translate>
        <SmallLoader v-if="loading" :show="loading" color="#0d6efd" size="5" />
    </button>
</template>


<script setup>
import { ref } from 'vue'
import SmallLoader from '@/components/SmallLoader.vue'
import { ResponseMsg } from '@appsbd/vue3-appsbd-libs'

const props = defineProps({
    user: { type: Object, required: true },
    defaultImage: { type: String, default: '' },
    loading: { type: Boolean, default: false },
    img_msg: { type: Object,required: false },
})

const emit = defineEmits(['submit'])

const fileInput = ref(null)
const selectedFile = ref(null)
const previewImage = ref(null)


const triggerFileInput = () => fileInput.value?.click()

function handleFileChange(e) {
    const file = e.target.files[0]
    if (!file) return

    selectedFile.value = file
    previewImage.value = URL.createObjectURL(file)
}

function submit() {
    if (!selectedFile.value) return
    emit('submit', selectedFile.value)
}

</script>
<style scoped lang="scss">
.avatar-wrapper {
    position: relative;
    margin-left: auto;
    margin-right: auto;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
    cursor: pointer;
}

.profile-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-overlay {
    color: #fff;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgb(0 0 0 / 26%);
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.avatar-overlay:hover {
    opacity: 1;
}
</style>

