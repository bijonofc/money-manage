<template>
    <Modal
        modal-size="modal-xl"
        ref="jobModal"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <!-- HEADER -->
        <template #header>
            <div class="modal-header-content">
                <h5 class="modal-title">
                    <translate>gbl.failed.job.details</translate>
                </h5>
            </div>
        </template>

        <!-- BODY -->
        <template #body>
            <div v-if="job.id" class="row g-3">

                <!-- BASIC INFO -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header" v-translate>job.info</div>
                        <div class="card-body small">
                            <p><strong><translate>gbl.uuid</translate>:</strong> {{ job.uuid }}</p>
                            <p><strong><translate>gbl.connection</translate>:</strong> {{ job.connection }}</p>
                            <p><strong><translate>gbl.queue</translate>:</strong> {{ job.queue }}</p>
                            <p><strong><translate>gbl.failed.at</translate>:</strong> {{ AppHelper.formatDate(job.failed_at) }}</p>
                        </div>
                    </div>
                </div>

                <!-- EXCEPTION -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between">
                            <span><translate>gbl.exception</translate></span>

                        </div>
                        <div class="card-body">
                            <pre class="exception-box">
                                  {{ job.exception }}
                            </pre>
                        </div>
                    </div>
                </div>

                <!-- PAYLOAD -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <span><translate>gbl.payload</translate></span>

                        </div>
                        <div class="card-body">
                            <div>
                                <vue-json-pretty indent="4" showIcon="true" deep="1" :data="job.payload" />
                            </div>
<!--                            <pre class="payload-box">
                                {{ JSON.stringify(JSON.parse(job.payload)) }}
                            </pre>-->
                        </div>
                    </div>
                </div>

            </div>
        </template>

        <!-- FOOTER -->
        <template #footer>
            <button class="btn btn-sm btn-secondary" @click="emitClose">
                <translate>gbl.close</translate>
            </button>
        </template>
    </Modal>
</template>

<script setup>
import {ref, reactive, onMounted, computed} from 'vue'
import {Modal} from '@appsbd/vue3-appsbd-libs'
import {useFailedJobStore} from '@/modules/AdminPanel/Job/FailedJobStore.js'
import AppsbdUtls from '@/libs/AppsbdUtls'
import AppHelper from "../../../libs/AppHelper.js";
import VueJsonPretty from "vue-json-pretty";

const props = defineProps({
    job_id: {type: Number, default: null}
})

const emit = defineEmits(['close'])

const jobStore = useFailedJobStore()
const jobModal = ref(null)
const isShowLoader = ref(false)



const job = reactive({
    id: null,
    uuid: '',
    connection: '',
    queue: '',
    exception: '',
    payload: '',
    failed_at: ''
})

const loaderStatusChange = (v) => {
    isShowLoader.value = v
}

const loadJob = async () => {
    if (!props.job_id) return
    try {
        jobModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.loading'))
        const res = await jobStore.getFailedJobDetails({job_id: props.job_id})

        if (res?.status) {
            Object.assign(job, res.data)
        }
    }catch (e) {
        console.log(e);
    }
    jobModal.value?.showLoader(false)
}

onMounted(loadJob)

/* ------------------ FORMATTERS ------------------ */




const formattedPayload = computed(() => {
    try {
        const json = JSON.parse(job.payload)
        const pretty = JSON.stringify(json, null, 2)
        return showFullPayload.value ? pretty : shortText(pretty)
    } catch {
        return showFullPayload.value
            ? job.payload
            : shortText(job.payload)
    }
})



const emitClose = () => emit('close')
</script>

<style scoped>
.exception-box,
.payload-box {
    background: #0f172a;
    color: #e5e7eb;
    padding: 12px;
    border-radius: 6px;
    max-height: 300px;
    overflow: auto;
    font-family: monospace;
    font-size: 13px;
    white-space: pre-wrap;
}
</style>
