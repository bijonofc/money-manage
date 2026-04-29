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
                change.google.login
           </span>
            </div>
        </template>

        <template #body>

            <div class="alert alert-warning" role="alert">
                {{AppsbdUtls.translateGettext('gbl.change.gauth',{type:props.is_sso == 'N' ? 'gbl.enable' : 'gbl.disable'})}}
            </div>
            <div class="row row-cols-1 g-3">
                <div class="col">
                    <InputField
                        name="password"
                        rules="required"
                        label="password"
                        placeholder="⚬⚬⚬⚬⚬⚬⚬"
                        prefix="Password"
                        type="password"
                        v-model="formData.password"
                    />
                </div>
            </div>

        </template>

        <template #footer>
            <button v-if="!props.is_force" class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
            <button type="submit" class="btn btn-sm btn-primary" v-translate>
                gbl.update
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
    is_sso:{
        default:'N',
    }
})

const emit = defineEmits(['close', 'reload'])

const userStore = useUserStore()

const userModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)

const formData = ref({ password: '',is_sso:'N'});
const oldData = reactive({})
const router=useRouter();


const loaderStatusChange = (v) => {
    isShowLoader.value = v
}
const submitForm = async () => {
    msg.value = {}
    userModal.value?.showLoader(true);
    formData.value.is_sso=props.is_sso=='N'?'Y':'N';
    try {
        let response=await userStore.changeGoogleAuth(formData.value);
        msg.value=response.msg;
        if (response.status)
        {
            userModal.value?.setMessageOnly(response.status)
            emit('reload')
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
