<template>
    <Modal :modal-msg="msg"
           modal-size="modal-md"
           ref="topupModal"
           @onSubmit="submitForm"
           @loading-status="loaderStatusChange"
           @close="emitClose">
        <template #header>
            <span> <translate>TopUp for </translate> {{agent.name}} </span>
        </template>

        <template #body>
            <div class="row gap-3">
                <div class="col-12">
                    <apbd-radio-button label="gbl.payment.type" name="type"  class="form-check-input-sm" :options="options" :val="val" v-model="topup.type"/>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <input-field type="number" label="gbl.amount" class="form-control form-control-sm text-end" v-model="topup.amount" name="amount" rules="required|numeric|min_value:1"/>
                </div>
                <div class="col-12 col-md-6">
                    <input-field label="gbl.ref.no" class="form-control form-control-sm" v-model="topup.ref_no" name="ref_no" rules="required"/>
                </div>
            </div>
            <div class="row gap-3">
                <div class="col-12">
                    <input-field as="textarea" label="gbl.description" class="form-control form-control-sm" v-model="topup.description" name="description" rules="required"/>
                </div>
            </div>
            <div class="row gap-3">
                <div class="col-12">
                    <div class="border rounded-3 p-3 bg-light-subtle">
                        <label class="form-label fw-medium text-muted d-flex align-items-center mb-0">
                            <i class="apb apb-file-alt me-1"></i>
                            <span v-translate>gbl.files</span>
                            <small class="text-muted ms-2 fw-normal">
                                ({{ AppsbdUtls.translateGettext('gbl.optional') }})
                            </small>
                        </label>

                        <image-uploader
                            v-model="topup.files"
                            input-id="file_path"
                            accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt"
                            :max-files="5"
                            :max-size="5"
                        />

                        <file-preview
                            v-if="uploadedImages.length"
                            class="mt-3"
                            :files="uploadedImages"
                            removable
                            @remove="$emit('remove', $event)"
                        />
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
            <button  type="submit"
                    class="btn btn-sm btn-primary" v-translate>
                gbl.make.topup
            </button>
        </template>
    </Modal>
</template>

<script setup>
import {onMounted, reactive, ref} from "vue";
import {ApbdRadioButton, InputField, Modal} from "@appsbd/vue3-appsbd-libs";
import AppsbdUtls from "@/libs/AppsbdUtls.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";
import {Field} from "vee-validate";
import {useAgentStore} from "@/modules/AdminPanel/Agent/AgentStore.js";
import FilePreview from "@/components/FilePreview.vue";
import ImageUploader from "@/components/ImageUploader.vue";
const props = defineProps({
    badge_id: {
        default: null
    },
    agent:{
        default:null
    }
})
const uploadedImages = ref([])

const emit = defineEmits(['close', 'reload'])
const msg = ref({})
const topupModal = ref(null);
const isShowLoader = ref(false)

const val = 'M';
const options = [
    {val: "M", title: "MFS"},
    {val: "B", title: "Banking"},
    {val: "C", title: "Cash"},
    {val: "O", title: "Online"},
]
const com_val = 'P';
const com_option = [
    {val: "P", title: "gbl.percentage"},
    {val: "F", title: "gbl.fixed"},
]
const oldData = reactive({})
const topup = reactive({
    type: 'M',
    ref_no: '',
    amount: '',
    description: '',
    ref_value:'',
    files:[]
})
const agentStore = useAgentStore();

onMounted(() => {
    //topupDetails()
});

const topupDetails = async () => {
    if (props.badge_id != null) {
        try {
            topupModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.loading', {name: 'badge'}));
            const response = await agentStore.getBadge({id: props.badge_id});
            if (response.status) {
                Object.assign(badge, response.data)
                Object.assign(oldData, response.data)
            }
        } catch (e) {
            console.log(e);
        }
        topupModal.value?.showLoader(false);
    }

}
const loaderStatusChange = (v) => {
    isShowLoader.value = v
}
const emitClose = () => {
    emit('close')
}
const submitForm = async () => {
    msg.value = {}
    try {
        topup.ref_value = props.agent.id;
        topupModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.adding', {name: 'topup'}))
        const response = await agentStore.addTopup(topup);
        msg.value = response.msg;
        if (response.status) {
            topupModal.value?.setMessageOnly(response.status)
            emit('reload')
        } else {
            topupModal.value?.showMsgOnly(response.msg, false)
        }
    } catch (e) {
        console.log(e);
    }
    topupModal.value?.showLoader(false)
}

const getFormData = () => {
    if (props.badge_id != null) {
        const diff = {}
        for (const key in badge) {
            if (badge[key] !== oldData[key]) {
                diff[key] = badge[key]
            }
        }
        return diff
    }
    return {...badge}
}

</script>

<style scoped lang="scss">

</style>
