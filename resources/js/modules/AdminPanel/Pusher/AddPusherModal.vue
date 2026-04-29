<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-lg"
        ref="pusherModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
      <div>
           <span v-if="props.pusher_id">
                {{AppsbdUtls.translateGettext('gbl.edit',{name:'Pusher'})}}
           </span>
          <span v-else>
               {{AppsbdUtls.translateGettext('gbl.add',{name:'Pusher'})}}
          </span>
      </div>
        </template>

        <template #body>
            <div class="row g-3">
                <div class= "col-12 col-md-6">
                    <input-field label="gbl.pusher.name" class="form-control" container-class="mb-0" v-model="pusher.title" name="title" rules="required"/>
                </div>
                <div class="col-12 col-md-6">
                    <input-field label="gbl.pusher.app.id" class="form-control" container-class="mb-0" v-model="pusher.push_app_id" name="app_id" rules="required"/>
                </div>
                <div class="col-12 col-md-6">
                    <input-field label="gbl.pusher.key" class="form-control" container-class="mb-0" v-model="pusher.push_key" name="push_key" rules="required"/>
                </div>
                <div class= "col-12 col-md-6">
                    <input-field label="gbl.secret" class="form-control" container-class="mb-0" v-model="pusher.push_secret" name="push_secret" rules="required"/>
                </div>
                <div class= "col-12 col-md-6">
                    <input-field label="gbl.cluster" class="form-control" container-class="mb-0" v-model="pusher.cluster" name="cluster" rules="required"/>
                </div>
                <div class="col-12 col-md-6">
                    <apbd-switch-button v-model="pusher.status" id="is_whatsapp"  container-class="form-switch-md" trueValue="A" falseValue="I">
                        <template #topLabel>
                            <translate>
                                is.active.?
                            </translate>
                        </template>
                    </apbd-switch-button>
                </div>
            </div>
        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
            <button  type="submit" class="btn btn-sm btn-primary">
                {{ props.pusher_id?AppsbdUtls.translateGettext('gbl.update'):AppsbdUtls.translateGettext('gbl.save')}}
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, watch, onMounted,computed } from 'vue'

import {
    ApbdFilterPanel,
    ApbdRadioButton,
    Modal,
    ResponseMsg,
    InputField,
    ApbdCheckBox,
    ApbdSwitchButton
} from '@appsbd/vue3-appsbd-libs'
import {Field, ErrorMessage, Form} from 'vee-validate'

import appsbdUtls from '@/libs/AppsbdUtls.js'
import Multiselect from '@vueform/multiselect'
import AppsbdUtls from '@/libs/AppsbdUtls.js'
import {usePusherStore} from '@/modules/AdminPanel/Pusher/PusherStore.js'
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import ContactNumberInput from "@/components/ContactNumberInput.vue";

const props = defineProps({
    pusher_id: {
        default: null
    }
})

const emit = defineEmits(['close', 'reload'])

const pusherStore = usePusherStore()
const loginStore = useLoginStore()


const pusherModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)

const pusher = reactive({title: '',push_app_id:'',push_key:'',push_secret:'',cluster:'',status:'A' })
const oldData = reactive({})

const val = ""

const loaderStatusChange = (v) => {
    isShowLoader.value = v
}

const dropdownCustomers = computed(() => {
    return pusherStore.customers.map((item) => {
        return {
            value: item.id,
            label: item.name
        }
    })
})

const resetForm = () => {
    pusherModal.value?.clearForm()
}

// Load if editing
const loadContact = async () => {
    if (props.pusher_id != null) {
        pusherModal.value?.showLoader(true, appsbdUtls.translateGettext('Loading type', { type: 'Pusher' }));
        const response = await pusherStore.getDetails({
            id: props.pusher_id
        })

        if (response.status) {
            Object.assign(pusher, response.data)
            Object.assign(oldData, response.data)
        }
        pusherModal.value?.showLoader(false)
    }
}

const getFormData = () => {
    if (props.pusher_id != null) {
        const diff = {}
        for (const key in pusher) {
            if (pusher[key] !== oldData[key]) diff[key] = pusher[key]
        }
        return diff
    }
    return { ...pusher }
}

const submitForm = async () => {
    msg.value = {}

    if (props.pusher_id != null) {
        const formData = getFormData()
        if (Object.keys(formData).length === 0) {
            msg.value = { error: ['No Change For Update'] }
            pusherModal.value?.showMsgOnly(msg.value, false)
            return
        }
        pusherModal.value?.showLoader(true,appsbdUtls.translateGettext('Updating %{type}', { type: 'Contact' }))
        const response = await pusherStore.updateContact({ id: props.pusher_id, ...formData })

        msg.value = response.msg
        if (response.status) {
             pusherModal.value?.setMessageOnly(response.status);
             emit('reload')
        } else {
            pusherModal.value?.showMsgOnly(response.msg, false)
        }
        pusherModal.value?.showLoader(false)

    } else {
        pusherModal.value?.showLoader(true, appsbdUtls.translateGettext('Adding %{type}', { type: 'Contact' }))
        const response = await pusherStore.addContact(pusher)
        msg.value = response.msg
        if (response.status) {
            pusherModal.value?.setMessageOnly(response.status)
            emit('reload')
            //resetForm()
        } else {
            pusherModal.value?.showMsgOnly(response.msg, false)
        }
        pusherModal.value?.showLoader(false)
    }
}

const emitClose = () => {
    emit('close')
}
onMounted(() => loadContact());
</script>

<style scoped lang="scss">
/* Add your styles here if needed */
</style>
