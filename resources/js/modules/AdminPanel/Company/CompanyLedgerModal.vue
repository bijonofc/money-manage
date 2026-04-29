<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-lg"
        ref="ledgerModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
            <div class="modal-header-content">
        <span v-if="props.ledger_id" class="modal-title">
          <translate :translate-params="{ name: 'ledger' }">gbl.details</translate>
        </span>
            </div>
        </template>

        <template #body>
            <div class="row row-cols-1 row-cols-sm-2 g-3">

                <!-- Left Card -->
                <div class="col">
                    <div class="card p-3 h-100">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <strong><translate>gbl.ledger.type</translate>: </strong>
                                <span :class="ledgerData.ledger_type === 'D' ? 'text-danger' : 'text-success'">
                                    {{ ledgerData.ledger_type === 'D' ? 'Debit' : 'Credit' }}
                                </span>
                            </div>
                            <div class="col-12 mb-2">
                                <strong><translate>gbl.amount</translate>: </strong>
                                <span :class="ledgerData.ledger_type === 'D' ? 'text-danger' : 'text-success'">
                                   {{ AppHelper.formatCurrency(ledgerData.amount) }}
                                </span>
                            </div>
                            <div class="col-12 mb-2">
                                <strong><translate>gbl.pre.balance</translate>: </strong>
                                <span>{{ AppHelper.formatCurrency(ledgerData.pre_balance) }}</span>
                            </div>
                            <div class="col-12 mb-2">
                                <strong><translate>gbl.current.balance</translate>: </strong>
                                <span>{{ AppHelper.formatCurrency(computeBalance(ledgerData.pre_balance, ledgerData.amount, ledgerData.ledger_type), 'bn') }}</span>
                            </div>
<!--                            <div class="col-12 mb-2">-->
<!--                                <strong><translate>gbl.user.name</translate>: </strong>-->
<!--                                <span>{{ ledgerData.user_name ?? "N/A" }}</span>-->
<!--                            </div>-->
                            <div class="col-12 mb-2">
                                <strong><translate>gbl.transaction.date</translate>: </strong>
                                <span>{{ formatDate(ledgerData.transaction_date) }}</span>
                            </div>


                        </div>
                    </div>
                </div>

                <!-- Right Card -->
                <div class="col">
                    <div class="card p-3 h-100">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <strong><translate>gbl.title</translate>: </strong>
                                <span class=""
                                      v-html="appsbdUtls.makeHtml(AppsbdUtls.translateGettext(ledgerData?.title,ledgerData?.title_params))">
                                </span>

                            </div>
                            <div class="col-12 mb-2" v-if="ledgerData.reference_type">
                                <strong><translate>gbl.ref.type</translate>: </strong>
                                <span>{{ getReferenceType(ledgerData.reference_type) }}</span>
                            </div>
                            <div class="col-12 mb-2" v-if="ledgerData.ref_label">
                                <strong><translate>gbl.ref.val</translate>: </strong>
                                <span class="text-dark">{{ ledgerData.ref_label }}</span>
                            </div>
                            <div class="col-12 mb-2">
                                <strong><translate>gbl.note</translate>: </strong>
                                <span>{{ ledgerData.note ?? "N/A" }}</span>
                            </div>

                            <div class="col-12 mb-2" v-if="ledgerData.ledger_files && ledgerData.ledger_files.length">
                            <div class="task-files-icon">
                               <span class="text-muted fw-semibold">
                                   <i class="apb apb-paperclip  me-2"></i>
                                   <translate>{{appsbdUtls.translateGettext('gbl.files')}}</translate>
                                   ({{ledgerData.ledger_files?.length}})
                               </span>
                            </div>
                                <div class="task-files">
                                    <file-preview :files="ledgerData.ledger_files" :removable="false"  />
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
import {ref, reactive, onMounted} from 'vue'
import {Modal} from '@appsbd/vue3-appsbd-libs'
import{useCompanyStore} from "@/modules/AdminPanel/Company/CompanyStore.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";
import AppHelper from "../../../libs/AppHelper.js";
import FilePreview from "@/components/FilePreview.vue";
import AppsbdUtls from "@/libs/AppsbdUtls.js";

const props = defineProps({
    ledger_id: {default: null}
})

const emit = defineEmits(['close', 'reload'])
const companyStore = useCompanyStore()
const ledgerData = reactive({
    title: '',
    title_params: [],
    ref_label: '',
    ref_type: '',
    note: '',
    transaction_date: '',
    amount: '',
    ledger_files: [],
    ledger_type: '',
    pre_balance: '',
    user_name: '',
})
const ledgerModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)

const loaderStatusChange = (v) => {
    isShowLoader.value = v
}

const loadingLedger = async () => {
    if (!props.ledger_id) return
    ledgerModal.value?.showLoader(true, appsbdUtls.translateGettext('load.ledger'))

    const response = await companyStore.getDetails({ledger_id: props.ledger_id})
    if (response.status)
    {
        Object.assign(ledgerData, response.data)

    }


    ledgerModal.value?.showLoader(false)
}

function computeBalance(prevBalance, amount, type) {
    const a = Number(prevBalance ?? 0)
    const b = Number(amount ?? 0)
    if (!isFinite(a) || !isFinite(b)) return 0
    return Math.round((type === 'D' ? a - b : a + b) * 100) / 100
}

function getReferenceType(reference_type) {

    if (reference_type.includes('Invoice')) return 'Invoice'
    if (reference_type.includes('PaymentRef')) return 'Transaction'
    if (reference_type.includes('User')) return 'User'

    return 'Reference'
}

function formatDate(dateStr) {
    if (!dateStr) return 'N/A'
    return AppHelper.formatDate(dateStr, 'DD MMM YYYY, hh:mm A')
}

const emitClose = () => emit('close')

onMounted(() => loadingLedger())
</script>

<style scoped lang="scss">
.text-success {
    font-weight: 600;
}

.text-danger {
    font-weight: 600;
}

.text-primary {
    font-weight: 500;
}

.card {
    border-radius: 0.5rem;
}
</style>
