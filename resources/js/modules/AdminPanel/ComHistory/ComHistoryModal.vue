<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-lg"
        ref="historyModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
            <div class="modal-header-content">
        <span v-if="props.com_history_id" class="modal-title">
          <translate>gbl.details</translate>
        </span>
            </div>
        </template>

        <template #body>
            <div class="row g-3">
                <div class="col-sm-12 col-md-6">
                    <div class="card p-3">
                        <p><strong><translate>gbl.customer.name</translate>:</strong> {{ historyData.customer_name ?? 'N/A' }}</p>
                        <p><strong><translate>gbl.creator.name</translate>:</strong> {{ historyData.creator_name ?? 'N/A' }}</p>
                        <p><strong><translate>gbl.type</translate>:</strong> {{ getTypeText(historyData.type) }}</p>
                        <p><strong><translate>gbl.status</translate>:</strong> {{ getStatusText(historyData.status) }}</p>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6">
                    <div class="card p-3">
                        <p><strong><translate>gbl.subject</translate>:</strong> {{ historyData.subject ?? 'N/A' }}</p>
                        <p><strong><translate>gbl.created.at</translate>:</strong> {{ AppHelper.formatDate(historyData.created_at) }}</p>
                        <p><strong><translate>gbl.next.follow.up</translate>:</strong> {{ AppHelper.formatDate(historyData.next_follow_up) }}</p>
                        <p><strong><translate>gbl.note</translate>:</strong> {{ historyData.note ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Bottom Row: Content + Dates -->
                <div class="col-12">
                    <div class="card p-3">
                        <h6><translate>gbl.content</translate></h6>
                        <div v-for="(value, key) in historyData.content ?? {}" :key="key">
                            <p>
                                <strong>{{ formatKey(key) }}: </strong>
                                <span v-if="Array.isArray(value)">
                                  <ul>
                                    <li v-for="(item, idx) in value" :key="idx">{{ item }}</li>
                                  </ul>
                                </span>
                                <span v-else>{{ value }}</span>
                            </p>
                        </div>


                    </div>
                </div>

            </div>
        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger" @click="emitClose" v-translate>gbl.close</button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Modal } from '@appsbd/vue3-appsbd-libs'
import { useComHistoryStore } from "@/modules/AdminPanel/ComHistory/ComHistoryStore.js"
import AppHelper from "@/libs/AppHelper.js"

const props = defineProps({ com_history_id: { default: null } })
const emit = defineEmits(['close', 'reload'])
const comHistoryStore = useComHistoryStore()
const historyData = reactive({})
const historyModal = ref(null)
const msg = ref({})

const loaderStatusChange = (v) => {}

const loadingHistory = async () => {
    if (!props.com_history_id) return
    historyModal.value?.showLoader(true, 'Loading History')
    const response = await comHistoryStore.getDetails({ com_history_id: props.com_history_id })
    if (response.status) Object.assign(historyData, response.data)
    historyModal.value?.showLoader(false)
}

function getTypeText(type) {
    switch(type){ case 'C': return 'Call'; case 'E': return 'Email'; case 'M': return 'Meeting'; case 'O': return 'Other'; default: return 'Unknown'; }
}
function getStatusText(status){
    switch(status){ case 'S': return 'Success'; case 'F': return 'Failed'; case 'P': return 'Pending'; default: return 'Unknown'; }
}

function formatKey(key)
{
    return key.replace(/_/g,' ').replace(/\b\w/g,l=>l.toUpperCase())
}
const emitClose = () => emit('close')
onMounted(()=>loadingHistory())
</script>

<style scoped lang="scss">

</style>
