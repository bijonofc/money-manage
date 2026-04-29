<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-lg"
        ref="contactModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
      <div>
           <span v-if="props.contact_id">
                {{AppsbdUtls.translateGettext('gbl.edit',{name:'Contact'})}}
           </span>
          <span v-else>
               {{AppsbdUtls.translateGettext('gbl.add',{name:'Contact'})}}
          </span>
      </div>
        </template>

        <template #body>
            <contact-form-fields v-model="contacts"/>
        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
            <button  type="submit" class="btn btn-sm btn-primary" v-translate>
                gbl.save.changes
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
import {useContactStore} from '@/modules/AdminPanel/Contact/ContactStore.js'
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import ContactNumberInput from "@/components/ContactNumberInput.vue";
import ContactFormFields from "@/components/ContactFormFields.vue";

const props = defineProps({
    contact_id: {
        default: null
    }
})

const emit = defineEmits(['close', 'reload'])

const contactStore = useContactStore()
const loginStore = useLoginStore()


const contactModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)

const contacts = ref({is_whatsapp: 'N',customer_id: '',phone:'',company_phone:'',email:'',company_email:'',company_name:'',contact_name:'' })
const oldData = ref({})

const val = ""
const val1 = []

const loaderStatusChange = (v) => {
    isShowLoader.value = v
}

const dropdownCustomers = computed(() => {
    return contactStore.customers.map((item) => {
        return {
            value: item.id,
            label: item.name
        }
    })
})

const resetForm = () => {
    contactModal.value?.clearForm()
}

// Load if editing
const loadContact = async () => {
    if (props.contact_id != null) {
        contactModal.value?.showLoader(true, appsbdUtls.translateGettext('Loading type', { type: 'Contact' }));
        const response = await contactStore.getDetails({
            id: props.contact_id
        })

        if (response.status) {
            Object.assign(contacts.value, response.data)
            Object.assign(oldData.value, response.data)
        }
        contactModal.value?.showLoader(false)
    }
}

const getFormData = () => {
    if (props.contact_id != null) {
        const diff = {}
        for (const key in contacts.value) {
            if (contacts.value[key] !== oldData.value[key]) {
                diff[key] = contacts.value[key]
            }
        }
        return diff
    }
    return { ...contacts.value }
}

const submitForm = async () => {
    msg.value = {}

    if (props.contact_id != null) {
        const formData = getFormData()
        if (Object.keys(formData).length === 0) {
            msg.value = { error: ['No Change For Update'] }
            contactModal.value?.showMsgOnly(msg.value, false)
            return
        }
        contactModal.value?.showLoader(true,appsbdUtls.translateGettext('Updating %{type}', { type: 'Contact' }))
        const response = await contactStore.updateContact({ id: props.contact_id, ...formData })

        msg.value = response.msg
        if (response.status) {
             contactModal.value?.setMessageOnly(response.status);
             emit('reload')
        } else {
            contactModal.value?.showMsgOnly(response.msg, false)
        }
        contactModal.value?.showLoader(false)

    } else {
        contactModal.value?.showLoader(true, appsbdUtls.translateGettext('Adding %{type}', { type: 'Contact' }))
        const response = await contactStore.addContact(contacts.value)
        msg.value = response.msg
        if (response.status) {
            contactModal.value?.setMessageOnly(response.status)
            emit('reload')
            //resetForm()
        } else {
            contactModal.value?.showMsgOnly(response.msg, false)
        }
        contactModal.value?.showLoader(false)
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
