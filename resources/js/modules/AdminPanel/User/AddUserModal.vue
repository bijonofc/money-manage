<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-lg"
        ref="userModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
      <div>
            <span v-if="props.user_id">
                {{AppsbdUtls.translateGettext('gbl.edit',{name:'gbl.user'})}}
           </span>
          <span v-else>
               {{AppsbdUtls.translateGettext('gbl.add',{name:'gbl.user'})}}
          </span>

      </div>
        </template>

        <template #body>
            <user-form :user="user"/>

        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
            <button v-if="props.user_id" type="submit" class="btn btn-sm btn-primary" v-translate>
                gbl.save.changes
            </button>
            <button v-else type="submit" class="btn btn-sm btn-primary" v-translate>
                gbl.save
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'

import {Modal,} from '@appsbd/vue3-appsbd-libs'

import {useUserStore} from '@/modules/AdminPanel/User/UserStore.js'

import AppsbdUtls from '@/libs/AppsbdUtls.js'

import UserForm from "@/components/UserForm.vue";

const props = defineProps({
    user_id: {
        default: null
    }
})

const emit = defineEmits(['close', 'reload'])

const userStore = useUserStore()

const userModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)

const user = reactive({ name: '',email:'',password:'',username:'',role_id:'1',status:'A',contact_no:'',is_whatsapp:'',country:'',state:'',zip_code:'',city:'',address:'' })
const oldData = reactive({})


const loaderStatusChange = (v) => {
    isShowLoader.value = v
}

const resetForm = () => {
    userModal.value?.clearForm()
}

const loadUser = async () => {
    if (props.user_id != null) {
        try {

            userModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.loading',{name:'user'}));
            const response = await userStore.getUser({
                user_id: props.user_id
            })
            if (response.status) {
                Object.assign(user, response.data)
                Object.assign(oldData, response.data)
            }

        }catch (e) {
            console.log(e);
        }
        userModal.value?.showLoader(false)
    }
}

const getFormData = () => {
    if (props.user_id != null) {
        const diff = {}
        for (const key in user) {
            if (user[key] !== oldData[key]) diff[key] = user[key]
        }
        return diff
    }
    return { ...user }
}

const submitForm = async () => {
    msg.value = {}

    if (props.user_id != null) {
        const formData = getFormData()
        if (Object.keys(formData).length === 0) {
            msg.value = { error: ['No Change For Update'] }
            userModal.value?.showMsgOnly(msg.value, false)
            return
        }
        userModal.value?.showLoader(true,AppsbdUtls.translateGettext('gbl.updating',{name:'user'}))
        const response = await userStore.updateUser({ id: props.user_id, ...formData })

        msg.value = response.msg
        if (response.status) {
             userModal.value?.setMessageOnly(response.status);
             emit('reload')
        } else {
            userModal.value?.showMsgOnly(response.msg, false)
        }
        userModal.value?.showLoader(false)

    } else {
        userModal.value?.showLoader(true,AppsbdUtls.translateGettext('gbl.adding',{name:'user'}))
        const response = await userStore.addUser(user)
        msg.value = response.msg
        if (response.status) {
            userModal.value?.setMessageOnly(response.status)
            emit('reload')
            //resetForm()
        } else {
            userModal.value?.showMsgOnly(response.msg, false)
        }
        userModal.value?.showLoader(false)
    }
}

const emitClose = () => {
    emit('close')
}
onMounted(() => loadUser());
</script>

<style scoped lang="scss">
/* Add your styles here if needed */
</style>
