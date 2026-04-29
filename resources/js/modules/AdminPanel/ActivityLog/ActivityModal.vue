<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-lg"
        ref="activityModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
            <div class="modal-header-content">
                <span v-if="props.activity_id" class="modal-title">
                    <translate :translate-params="{ name: 'activity' }">gbl.details</translate>
                </span>
            </div>
        </template>

        <template #body>
            <div class="row row-cols-1">
                <div class="col">
                    <div class="card">
                        <div>
                            <div class="card-header d-flex align-items-center justify-content-between">
                               <span>{{ AppsbdUtls.translateGettext(activity.des, activity.des_param) +' '+activity.human_time}} </span>
                                <span>{{ AppHelper.formatDate(activity.created_at) }} </span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                         <span v-translate>alog.log</span>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <span> {{ AppsbdUtls.translateGettext(activity.des, activity.des_param)}}</span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                         <span v-translate>alog.time</span>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <span> {{ AppHelper.formatDate(activity.created_at) }}</span>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                     <span v-translate>alog.ip</span>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <span>{{ activity.ip_address }}</span>
                                    </div>
                                    <div class="col-md-4">
                                         <span v-translate>alog.url</span>
                                    </div>
                                    <div class="col-md-8">
                                        <span>{{ activity.url }}</span>
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
import { useSettingStore } from '@/modules/AdminPanel/Settings/SettingStore.js'
import AppsbdUtls from '@/libs/AppsbdUtls.js'
import AppHelper from "@/libs/AppHelper.js";

const props = defineProps({
    activity_id: {
        default: null
    }
})

const emit = defineEmits(['close', 'reload'])

const settingStore = useSettingStore()

const activityModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)
const isLoading = ref(false)

const activity = reactive({ des: '', des_param: {}, created_at: '', ip_address: '', url: '' })

const loaderStatusChange = (v) => {
    isShowLoader.value = v
}

const loadLog = async () => {
    if (props.activity_id != null) {
        msg.value = {}
        activityModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.loading', { name: 'alog' }))
        isLoading.value = true
        const response = await settingStore.getLog({
            activity_id:props.activity_id
        })
        msg.value = response.msg
        if (response.status) {
            Object.assign(activity, response.data)
        } else {
            activityModal.value?.setMessageOnly(response.msg, false)
        }
        isLoading.value = false
        activityModal.value?.showLoader(false)
    }
}

const emitClose = () => {
    emit('close')
}

onMounted(() => loadLog())
</script>

<style scoped lang="scss">


</style>
