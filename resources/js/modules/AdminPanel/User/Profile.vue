<template>
    <div class="profile-wrapper">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-light border-0 text-center py-3">
                <h5 class="mb-0 fw-semibold">
                    <translate>profile.information</translate>
                </h5>
            </div>

            <div class="card-body p-4">

                <div class="profile-layout row g-4">
                    <div class="col-12 col-md-4 text-center">
                        <ProfileAvatar :user="user" :default-image="defaultImage"  :loading="loadingImage" :img_msg="img_msg" @submit="uploadProfileImage"/>

                    </div>

                    <div class="col-12 col-md-8">
                        <profile-info :user="user" @reload="refresh" @openProfileModal="openProfileModal($event)" @openPasswordModal="openPasswordModal($event)"/>
                    </div>
                </div>
            </div>
        </div>


        <AddUserModal v-if="isShowModal" :user_id="userId" @close="closeModal" @reload="refresh" />
        <PasswordChange v-if="isPasswordModal" :user_id="userId" @close="closePasswordModal" />
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue'

import { useUserStore } from '@/modules/AdminPanel/User/UserStore.js'
import { useLoginStore } from '@/modules/AdminPanel/User/loginStore.js'
import PasswordChange from '@/modules/AdminPanel/User/PasswordChange.vue'
import AddUserModal from '@/modules/AdminPanel/User/AddUserModal.vue'

import ProfileInfo from "@/components/ProfileInfo.vue";
import ProfileAvatar from "@/components/ProfileAvatar.vue";


const userStore = useUserStore()
const loginStore = useLoginStore()

const user = reactive({
    name: '',
    username: '',
    email: '',
    contact_no: '',
    country: '',
    state: '',
    city: '',
    is_sso: 'N',
    zip_code: '',
    address: '',
    image: ''
})
const oldData = reactive({})
const img_msg = ref({})
const isLoading = ref(false)
const isShowLoader = ref(false)
const isPasswordModal = ref(false)
const isShowModal = ref(false)
const userId = ref(null)
import defaultImage from '@/assets/header.jpg';
const previewImage = ref(null)
const fileInput = ref(null)
const selectedFile = ref(null)
const loadingImage = ref(false)




async function uploadProfileImage(file) {
    if (!file) return

    try {
        loadingImage.value = true
        const response = await userStore.updateImage({

            image: file
        })

        img_msg.value = response.msg

        if (response.status) {
            user.image_url = response.data.image_url

            loginStore.loggedUserData.image = response.data.image

            loginStore.loggedUserData.image_url = response.data.image_url
        }
    } catch (err) {
        console.error(err)
    } finally {
        loadingImage.value = false
    }
}

function refresh() {
    console.log("called refresh");
    getUserData()
}
function openPasswordModal(id) {

    userId.value = id
    isPasswordModal.value = true
}
function closePasswordModal() {
    userId.value = null
    isPasswordModal.value = false
}
function closeModal() {
    userId.value = null
    isShowModal.value = false
}
function openProfileModal(id) {

    userId.value = id
    isShowModal.value = true
}
const getUserData = async () => {
    try {
        isLoading.value = true
        let response = await userStore.getUserProfile();
        if (response.status) {
            Object.assign(user, response.data)
            Object.assign(oldData, response.data)
        }
    } catch (e) {
        console.log(e)
    }
    isLoading.value = false
}
onMounted(() => {
    getUserData()
})
</script>

<style lang="scss" scoped>
.profile-wrapper {
    max-width: 800px;
    margin: 40px auto;
}

</style>
