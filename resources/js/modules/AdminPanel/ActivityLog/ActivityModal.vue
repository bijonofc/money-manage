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
            <div class="modal-header-content d-flex align-items-center gap-2">
                <span v-if="props.activity_id" class="modal-title fw-bold">
                    <i class="apb apb-file-06 me-1"></i>
                    <translate :translate-params="{ name: 'activity' }">gbl.details</translate>
                </span>
            </div>
        </template>

        <template #body>
            <div class="row row-cols-1 g-3">
                <div class="col">
                    <div class="card border shadow-none">
                        <div class="card-header bg-light d-flex align-items-center justify-content-between py-2">
                            <span class="d-flex align-items-center gap-2">
                                <span :class="getEventBadgeClass(activity.event)">{{ activity.event ? activity.event.toUpperCase() : 'ACTIVITY' }}</span>
                                <span class="fw-semibold">{{ activity.human_time }}</span>
                            </span>
                            <span class="text-muted small">
                                <i class="apb apb-calendar me-1"></i>
                                {{ AppHelper.formatDate(activity.created_at) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3 text-muted">
                                    <span v-translate>alog.log</span>
                                </div>
                                <div class="col-md-9 fw-medium">
                                    <span v-html="makeHtml(AppsbdUtls.translateGettext(activity.des, activity.des_param))"></span>
                                </div>

                                <div class="col-md-3 text-muted" v-if="activity.user">
                                    <span v-translate>alog.user</span>
                                </div>
                                <div class="col-md-9" v-if="activity.user">
                                    <span>{{ activity.user.name }} ({{ activity.user.email || activity.user.username }})</span>
                                </div>

                                <div class="col-md-3 text-muted">
                                    <span v-translate>alog.time</span>
                                </div>
                                <div class="col-md-9">
                                    <span>{{ AppHelper.formatDate(activity.created_at) }} ({{ activity.created_at }})</span>
                                </div>

                                <div class="col-md-3 text-muted">
                                    <span v-translate>alog.ip</span>
                                </div>
                                <div class="col-md-9">
                                    <code>{{ activity.ip_address || 'N/A' }}</code>
                                </div>

                                <div class="col-md-3 text-muted" v-if="activity.url">
                                    <span v-translate>alog.url</span>
                                </div>
                                <div class="col-md-9 text-break" v-if="activity.url">
                                    <code class="small">{{ activity.url }}</code>
                                </div>

                                <div class="col-md-3 text-muted" v-if="activity.user_agent">
                                    <span>User Agent</span>
                                </div>
                                <div class="col-md-9 text-break small text-secondary" v-if="activity.user_agent">
                                    <span>{{ activity.user_agent }}</span>
                                </div>

                                <div class="col-12 mt-3" v-if="hasProperties">
                                    <div class="p-3 bg-light rounded border">
                                        <div class="fw-semibold mb-2 small text-uppercase text-secondary">Action Details / Snapshot</div>
                                        <pre class="m-0 small text-break" style="max-height: 200px; overflow-y: auto;">{{ JSON.stringify(activity.properties, null, 2) }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <button class="btn btn-sm btn-secondary" @click="emitClose" v-translate>
                gbl.close
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { Modal } from '@appsbd/vue3-appsbd-libs'
import { useActivityStore } from '@/modules/AdminPanel/ActivityLog/ActivityStore.js'
import AppsbdUtls from '@/libs/AppsbdUtls.js'
import AppHelper from "@/libs/AppHelper.js"

const props = defineProps({
    activity_id: {
        default: null
    }
})

const emit = defineEmits(['close', 'reload'])

const activityStore = useActivityStore()

const activityModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)
const isLoading = ref(false)

const activity = reactive({
    id: null,
    event: '',
    des: '',
    des_param: {},
    created_at: '',
    human_time: '',
    ip_address: '',
    url: '',
    user_agent: '',
    user: null,
    properties: null
})

const hasProperties = computed(() => {
    return activity.properties && Object.keys(activity.properties).length > 0
})

const loaderStatusChange = (v) => {
    isShowLoader.value = v
}

function getEventBadgeClass(event) {
    const map = {
        login: 'badge bg-primary',
        logout: 'badge bg-secondary',
        created: 'badge bg-success',
        updated: 'badge bg-warning text-dark',
        deleted: 'badge bg-danger',
    }
    return map[event] || 'badge bg-info'
}

function makeHtml(text) {
    if (!text) return ''
    text = text.replace(/^# (.*$)/gim, '<h1>$1</h1>')
    text = text.replace(/^## (.*$)/gim, '<h2>$1</h2>')
    text = text.replace(/^### (.*$)/gim, '<h3>$1</h3>')
    text = text.replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>')
    text = text.replace(/\*(.*?)\*/gim, '<em>$1</em>')
    text = text.replace(/\[color:(.*?)\](.*?)\[\/color\]/gim, '<span style="color: $1">$2</span>')
    text = text.replace(/\n/gim, '<br>')
    return text
}

const loadLog = async () => {
    if (props.activity_id != null) {
        msg.value = {}
        activityModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.loading', { name: 'alog' }))
        isLoading.value = true
        try {
            const response = await activityStore.getLog({
                activity_id: props.activity_id
            })
            if (response && response.status) {
                Object.assign(activity, response.data)
            } else {
                activityModal.value?.setMessageOnly(response?.msg || 'Failed to load', false)
            }
        } catch (e) {
            console.error(e)
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

