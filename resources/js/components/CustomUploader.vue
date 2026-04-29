<template>
    <div class="d-flex flex-column align-items-center h-100">
        <div class="flex-grow-1 d-flex align-items-center justify-content-center w-100">
            <div class="text-center w-100">
                <ResponseMsg :message="msg" />
                <img
                    class="img-thumbnail img-fluid profile-img"
                    :src="previewImage || imageUrl || defaultImage"
                    alt="Profile"
                />
            </div>
        </div>

        <div class="mt-3 w-100 text-center">
            <button type="button" class="btn btn-primary" @click="triggerFileInput" :disabled="loading">
                <span v-if="loading">Uploading...</span>
                <span v-else>Upload Image</span>
            </button>
            <input
                ref="fileInput"
                type="file"
                class="d-none"
                accept="image/*"
                @change="handleFileChange"
            />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { ResponseMsg } from '@appsbd/vue3-appsbd-libs'
import { useUserStore } from '@/modules/User/UserStore.js'
import { useLoginStore } from '@/modules/User/loginStore.js'

const props = defineProps({
    imageUrl: String,
    defaultImage: {
        type: String,
        default:
            'https://images.pexels.com/photos/1704488/pexels-photo-1704488.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500'
    }
})

const emit = defineEmits(['updated'])

const fileInput = ref(null)
const previewImage = ref(null)
const msg = ref({})
const loading = ref(false)

const userStore = useUserStore()
const loginStore = useLoginStore()

const triggerFileInput = () => {
    fileInput.value?.click()
}

const handleFileChange = async (event) => {
    const file = event.target.files[0]
    if (!file) return

    previewImage.value = URL.createObjectURL(file)
    msg.value = {}
    loading.value = true

    try {
        const response = await userStore.updateImage({
            id: loginStore.loggedUserData.id,
            image: file
        })
        msg.value = response.msg

        if (response.status) {
            emit('updated', response.data.image_url)
            previewImage.value = response.data.image_url
        }
    } catch (err) {
        console.error(err)
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>

</style>
