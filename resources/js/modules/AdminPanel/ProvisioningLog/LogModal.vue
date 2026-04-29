<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-lg"
        ref="logModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
            <div class="modal-header-content">
                <span v-if="props.log_id" class="modal-title">
                    <translate :translate-params="{ name: 'log' }">gbl.details</translate>
                </span>
            </div>
        </template>

        <template #body>
            <div class="row row-cols-1">
                <div class="col">
                    <div class="card">
                        <div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                         <span v-translate>gbl.customer.name</span>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <span> {{ log.customer_name }}</span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                         <span v-translate>gbl.message</span>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <span> {{ log.message }}</span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                     <span v-translate>gbl.status</span>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <span>{{ getStatus(log.status) }}</span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                     <span v-translate>gbl.log.type</span>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <span>{{ getLogType(log.log_type) }}</span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                     <span v-translate>gbl.created.at</span>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <span>{{ AppHelper.formatDate(log.created_at) }}</span>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Modal } from '@appsbd/vue3-appsbd-libs'
import {useLogStore} from "@/modules/AdminPanel/ProvisioningLog/LogStore.js";
import AppsbdUtls from '@/libs/AppsbdUtls.js'
import AppHelper from "../../../libs/AppHelper.js";

const props = defineProps({
    log_id: {
        default: null
    }
})

const emit = defineEmits(['close', 'reload'])

const logStore=useLogStore();

const logModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)
const isLoading = ref(false)

const log = reactive({ })

const loaderStatusChange = (v) => {
    isShowLoader.value = v
}

const loadLog = async () => {
    if (props.log_id != null) {
        msg.value = {}
        logModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.loading', { name: 'log' }))
        isLoading.value = true
        const response = await logStore.getLog({
            log_id:props.log_id
        })
        msg.value = response.msg
        if (response.status) {
            Object.assign(log, response.data)
        } else {
            logModal.value?.setMessageOnly(response.msg, false)
        }
        isLoading.value = false
        logModal.value?.showLoader(false)
    }
}

const emitClose = () => {
    emit('close')
}
function getStatus(status) {
    if(status=='S') return 'Success';
    if(status=='F') return 'Failed';
    if(status=='I') return 'Info';
    if(status=='W') return 'Warning';
    return 'Unknown';
}
function getLogType(type) {
    if(type=='D') return 'Database creating';
    if(type=='S') return 'Site creating';
    if(type=='P') return 'Provisioning';
    if(type=='C') return 'Completed';
    return 'Unknown';
}
onMounted(() => loadLog())
</script>

<style scoped lang="scss">


</style>
