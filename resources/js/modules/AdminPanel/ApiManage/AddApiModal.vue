<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-lg"
        ref="apiModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
      <div>
           <span v-if="props.api_id">
                {{AppsbdUtls.translateGettext('gbl.edit',{name:'api'})}}
           </span>
          <span v-else>
               {{AppsbdUtls.translateGettext('gbl.add',{name:'api'})}}
          </span>
      </div>
        </template>

        <template #body>
            <div class="row">
                <div class= "col-12 col-md-6 mb-2" >
                    <input-field container-class="mb-0" label="gbl.title" class="form-control form-control-sm" v-model="api.title" name="title" rules="required"/>
                </div>
                <div class= "col-12 col-md-6 mb-2" >
                    <label class="d-flex form-label" for="api_key">
                            <translate class="me-2">
                                gbl.api.key
                            </translate>
                        <div v-if="!api_id" class="form-check form-switch form-switch-sm d-flex align-items-center ps-0">
                            (<label class="form-check-label me-5 ms-2 mt-0" for="is_all_outlet">
                                <translate>gbl.auto.generate</translate>?
                            </label>
                            <input class="form-check-input mt-0 me-2"  v-model="api.is_auto_generate" type="checkbox"
                                   true-value="Y"
                                   false-value="N"
                                   role="switch" id="is_all_outlet">
                            )
                        </div>
                    </label>

                    <Field label="gbl.api.key" type="text" :disabled="api.is_auto_generate=='Y'||api_id" v-model="api.api_key" :rules="api.is_auto_generate=='Y'||api_id?'':'required'" name="api_key" id="api_key" class="form-control form-control-sm"/>
                    <ErrorMessage name="api_key" class="apbd-v-error"/>
                </div>
            </div>
            <div class="row">
                <div class= "col-12 col-md-6 mb-2"  >
                    <input-field container-class="mb-0" label="gbl.ref.id" class="form-control form-control-sm" v-model="api.ref_id" name="ref_id" rules=""/>
                </div>
                <div class="col-12 col-md-6 mb-2" >
                    <input-field container-class="mb-0" label="gbl.host.ip" class="form-control form-control-sm" v-model="api.host_ip" name="host_ip" rules="" />
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-12 col-md-6">
                    <label class="form-label" v-translate>Permissions</label>
                    <div class="d-flex flex-column mb-1">
                        <div
                            v-for="t in types"
                            :key="t.val"
                            class="d-flex justify-content-between border rounded-1 align-items-center px-2 py-1 mb-1"
                        >

                            <span class="fw-semibold">{{ appsbdUtls.translateGetMsg(t.title) }}</span>
                            <div class="form-check form-switch-sm form-switch m-0">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    :id="t.val"
                                    :checked="api.permissions.includes(t.val)"
                                    @change="togglePermission(t.val, $event)"
                                />
                            </div>
                        </div>
                    </div>
<!--                    <apbd-radio-button  label="gbl.type" name="type"  class="form-check-input-sm" :options="types" v-model="api.type"/>-->
                </div>
                <div class="col-12 col-md-6">
                    <apbd-radio-button label="gbl.status" name="status"  class="form-check-input-sm" :options="options"  v-model="api.status"/>
                </div>
            </div>
        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
            <button v-if="props.api_id" type="submit" class="btn btn-sm btn-primary" v-translate>
                gbl.save.changes
            </button>
            <button v-else type="submit" class="btn btn-sm btn-primary" v-translate>
                gbl.save
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'

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
import {useApiStore} from "@/modules/AdminPanel/ApiManage/ApiStore.js";

const props = defineProps({
    api_id: {
        default: null
    }
})

const emit = defineEmits(['close', 'reload'])

const apiStore = useApiStore()


const apiModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)

const api = reactive({ title: '',api_key:'',ref_id:'',permissions:[],host_ip:'',status:'A',is_auto_generate:'N'})
const oldData =  reactive({})
const options= [
    {val: "A", title: "gbl.active"},
    {val: "I", title: "gbl.inactive"},
];
const types= [
    {val: "W", title: "api.WhatsApp"},
    {val: "S", title: "api.SMS"},
    {val: "E", title: "api.Email"},
];
const val = ""
const val1 = []

const loaderStatusChange = (v) => {
    isShowLoader.value = v
}
function togglePermission(value, event) {
    if (event.target.checked) {
        if (!api.permissions.includes(value)) {
            api.permissions.push(value);
        }
    } else {
        api.permissions = api.permissions.filter((v) => v !== value);
    }
}

const resetForm = () => {
    apiModal.value?.clearForm()
}

// Load if editing
const loadApi = async () => {
    if (props.api_id != null) {
        apiModal.value?.showLoader(true, appsbdUtls.translateGettext('Loading Api Data'));
        const response = await apiStore.getApi({
            id: props.api_id
        })

        if (response.status) {
            Object.assign(api, response.data)
            oldData.value = JSON.parse(JSON.stringify(response.data))
        }
        apiModal.value?.showLoader(false)
    }
}

const getFormData = () => {
    if (props.api_id != null) {
        const diff = {}
        for (const key in api) {
            if (api[key] !== oldData.value[key]) diff[key] = api[key]
        }
        return diff
    }
    return { ...api }
}

const submitForm = async () => {
    msg.value = {}

    if (props.api_id != null) {
        const formData = getFormData()
        if (Object.keys(formData).length === 0) {
            msg.value = { error: ['No Change For Update'] }
            apiModal.value?.showMsgOnly(msg.value, false)
            return
        }
        apiModal.value?.showLoader(true,appsbdUtls.translateGettext('Updating api'))
        const response = await apiStore.updateApi({ id: props.api_id, ...formData })

        msg.value = response.msg
        if (response.status) {
             apiModal.value?.setMessageOnly(response.status);
             emit('reload')
        } else {
            apiModal.value?.showMsgOnly(response.msg, false)
        }
        apiModal.value?.showLoader(false)

    } else {
        apiModal.value?.showLoader(true, appsbdUtls.translateGettext('Adding Api'))
        const response = await apiStore.addApi(api)
        msg.value = response.msg
        if (response.status) {
            apiModal.value?.setMessageOnly(response.status)
            emit('reload')
            //resetForm()
        } else {
            apiModal.value?.showMsgOnly(response.msg, false)
        }
        apiModal.value?.showLoader(false)
    }
}

const emitClose = () => {
    emit('close')
}
onMounted(() => loadApi());
</script>

<style lang="scss">
.cs-mb{
    margin-bottom: 0.125rem !important;
}
/* Add your styles here if needed */
</style>
