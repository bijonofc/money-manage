<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-md"
        ref="userModal"
        @onSubmit="submitForm"
        :hideCrossBtn="props.is_force"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
            <div>
            <span v-if="props.user_id" v-translate>
                change.password
           </span>
            </div>
        </template>

        <template #body>
           <change-password-form :isForce="props.is_force" :formData="formData"/>

        </template>

        <template #footer>
            <button v-if="!props.is_force" class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
            <router-link v-if="props.is_force" class="btn btn-sm btn-danger" to="/logout" v-translate>
                gbl.logout
            </router-link>
            <button v-if="props.user_id && props.is_force" type="submit" class="btn btn-sm btn-primary" v-translate>
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

import {
    ApbdFilterPanel,
    ApbdRadioButton,
    Modal,
    ResponseMsg,
    ApbdDatePicker,
    InputField,
    ApbdDropdown
} from '@appsbd/vue3-appsbd-libs'
import { Field, ErrorMessage } from 'vee-validate'
import {useUserStore} from '@/modules/AdminPanel/User/UserStore.js'
import ChangePasswordForm from '@/components/ChangePasswordForm.vue'
import AppsbdUtls from '@/libs/AppsbdUtls.js'
import {useRouter} from "vue-router";

const props = defineProps({
    user_id: {
        default: null
    },
    is_force:{
        default:false,
    }
})

const emit = defineEmits(['close', 'reload'])

const userStore = useUserStore()

const userModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)

const formData = ref({ old_password: '', new_password: '', new_password_confirmation: '',is_force:'N'});
const oldData = reactive({})
const router=useRouter();


const loaderStatusChange = (v) => {
    isShowLoader.value = v
}
const submitForm = async () => {
    msg.value = {}
    userModal.value?.showLoader(true);
    try {
        if(props.is_force)
        {
            formData.value.is_force='Y';
        }
        let response=await userStore.changePassword(formData.value);
        msg.value=response.msg;
        if (response.status)
        {
            userModal.value?.setMessageOnly(response.status)

        }else {
            userModal.value?.showMsgOnly(response.msg, false)
        }
    }catch (e) {
        console.log(e);
    }
    userModal.value?.showLoader(false)
}

const emitClose = () => {
    emit('close')
}

</script>

<style scoped lang="scss">
/* Add your styles here if needed */
</style>
