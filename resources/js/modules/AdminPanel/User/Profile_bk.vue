<template>


    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">

                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                       <CustomUploader />
                    </div>
                    <div class="d-flex gap-5  align-items-center">

                        <settings-form ref="profileForm" :on-submit="onSubmit">

                            <message :msgs="profile_msg" />
                            <div v-if="isLoading" class="text-center">
                                <AppLoader :msg="$gettext('Loading User Data...')" />
                            </div>
                                <div class="row row-cols-1 row-cols-md-4 g-3">
                                    <input-field label="name" v-model="user.name" name="name" rules="required" />
                                    <input-field label="gbl.email" v-model="user.email" name="email" rules="required" />
                                    <input-field label="gbl.username" disable v-model="user.username" name="username" rules="required" />
                                    <input-field label="gbl.contact" v-model="user.contact_no" name="contact_no" rules="required" />
                                </div>
                                <div class="row row-cols-1 row-cols-md-4 g-3">
                                    <input-field label="gbl.country" v-model="user.country" name="country" rules="required" />
                                    <input-field label="gbl.state" v-model="user.state" name="state" rules="required" />
                                    <input-field label="gbl.city" v-model="user.city" name="city" rules="required" />
                                    <input-field label="gbl.zip" v-model="user.zip_code" name="zip_code" rules="required" />
                                </div>
                                <div class="row row-cols-1 g-3 mb-3">
                                    <div class="col">
                                        <label for="address" class="form-label ps-0" v-translate>gbl.address</label>
                                        <Field
                                            as="textarea"
                                            class="form-control form-control-sm"
                                            id="address"
                                            name="address"
                                            v-model="user.address"
                                            :placeholder="appsbdUtls.translateGettext('gbl.address')"
                                            style="height: 70px;"
                                        />
                                    </div>

                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-theme rounded" v-translate>
                                        gbl.save
                                    </button>
                                </div>
                        </settings-form>
                    </div>


                </div>
            </div>
        </div>


        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <settings-form ref="passForm" :on-submit="changePassword">
                        <message :msgs="pass_msg" />
                        <div class="add-form">
                            <InputField
                                name="old_password"
                                rules="required"
                                label="old.password"
                                placeholder="⚬⚬⚬⚬⚬⚬⚬"
                                prefix="Old password"
                                type="password"
                                v-model="formData.old_password"

                            />
                            <InputField
                                name="new_password"
                                rules="required|min:6"
                                label="new.password"
                                placeholder="⚬⚬⚬⚬⚬⚬⚬"
                                prefix="New Password"
                                type="password"
                                v-model="formData.new_password"

                            />

                            <InputField
                                name="new_password_confirmation"
                                rules="required|confirmed:@new_password"
                                label="new.password.confirmation"
                                placeholder="⚬⚬⚬⚬⚬⚬⚬"
                                prefix="Password Confirm"
                                type="password"
                                v-model="formData.new_password_confirmation"

                            />
                            <div class="text-center">
                                <button type="submit" class="btn btn-theme rounded" v-translate>
                                    change.password
                                </button>
                            </div>
                        </div>
                    </settings-form>

                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { Field, ErrorMessage } from 'vee-validate'

import Message from '@/components/Message'
import SettingsForm from '@/components/SettingsForm'
import { InputField ,AppLoader} from '@appsbd/vue3-appsbd-libs'
import appsbdUtls from '@/libs/AppsbdUtls.js'
import ImageUploader from '@/components/ImageUploader.vue'
import CustomUploader from '@/components/CustomUploader.vue'

const userStore=useUserStore()
const loginStore=useLoginStore()

const formData = ref({
    old_password: '',
    new_password: '',
    new_password_confirmation: '',
})

const user = reactive({

    name: '',
    username: '',
    email: '',
    contact_no: '',
    country: '',
    state: '',
    city: '',
    zip_code: '',
    address: '',
    ip_address: '',
});
const oldData = reactive({});

const pass_msg = ref({})
const profile_msg = ref({})
const userData = ref({})
const isLoading = ref(false)
const passForm = ref(null)
const profileForm = ref(null)

const onSubmit = async () => {
 try {
     if (loginStore.loggedUserData?.id != null)
     {
         const formData = getFormData()
         if (Object.keys(formData).length === 0) {
             profile_msg.value = { error: ['No Change For Update'] }
             return
         }
         const response = await userStore.updateUser({ id: loginStore.loggedUserData?.id, ...formData })
         profile_msg.value = response.msg
     }
 }catch (e) {
     console.log(e);
 }
}
const changePassword= async () => {
    try {
     let response=await userStore.changePassword(formData.value);
        pass_msg.value=response.msg;
        if (response.status)
        {
            passForm.clearForm();
        }
    }catch (e) {
        console.log(e);
    }
}
const getUserData=async () => {
    try {
        isLoading.value=true;
        let response = await userStore.getUser({user_id:loginStore.loggedUserData?.id});
        if (response.status){
            Object.assign(user, response.data)
            Object.assign(oldData, response.data)
        }
    }catch (e) {
        console.log(e);
    }

    isLoading.value=false;
}
const getFormData = () => {
    if (loginStore.loggedUserData?.id != null) {
        const diff = {}
        for (const key in user) {
            if (user[key] !== oldData[key]) diff[key] = user[key]
        }
        return diff
    }
    return { ...user }
}
onMounted(() => {
    getUserData()
})
</script>

<style lang="scss" scoped>
.card {
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
}
.profile-img {
    text-align: center;
}

img.rounded-circle {
    border: 1px solid #dee2e6;
    box-shadow: 0 0 6px rgba(0, 0, 0, 0.05);
}
</style>
