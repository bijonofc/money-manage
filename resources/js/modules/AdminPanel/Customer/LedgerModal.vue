<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-xl"
        ref="ledgerModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >

        <template #header>
            <div class="d-flex align-items-center gap-2 w-100">
                <h6 class="mb-0" v-translate>transaction.ledger</h6>
            </div>
        </template>

        <!-- ================= Body ================= -->
        <template #body>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
<!--                                <div class="col-sm-8">-->
<!--                                    <apbd-filter-panel :is-single="true" @searchFilter="searchData" @reset="clearSearch" />-->
<!--                                </div>-->
                                <div class="col-sm-12  d-flex align-items-center justify-content-end gap-2">
                                    <button class="btn btn-sm btn-primary" @click="refreshGrid">
                                        <i class="apb apb-refresh-ccw-alt"> </i>
                                        <span class="ms-2" v-translate>gbl.reload</span>
                                    </button>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="col-sm-12 mt-3">
                        <elite-grid
                            :is-rounded="true"
                            :is-group-separate-head="true"
                            action-width="100px"
                            :columns="data_column"
                            :show-loader="isShowLoader"
                            :show-header="false"
                            :grid-data="gridData"
                            :show-action-column="false"
                            :is-show-row-index-column="true"
                            @load-data="eliteGridLoadData"
                        >
                            <template v-slot:slot-loader>
                                <APBDGridLoader msg="ledger.loading" />
                            </template>
                            <template v-slot:slot-no-record>
                                {{
                                    appsbdUtls.translateGettext('No %{type} found', { type: 'ledger' })
                                }}
                            </template>
                            <template v-slot:slotledger_type="{rowitem}">
                                  <span :class="rowitem.ledger_type=='D'?'text-danger':'text-success'">
                                    {{ getType(rowitem.ledger_type) }}
                                  </span>
                            </template>
                            <template v-slot:slottransaction_date="{ rowitem }">
                                <span>{{ AppHelper.formatDate(rowitem.transaction_date) }}</span>
                            </template>
                            <template v-slot:slotamount="{rowitem}">
                                <ledger-amount-format :amount="rowitem.amount" :ledger-type="rowitem.ledger_type"/>
                            </template>

                            <template v-slot:slotpre_balance="{rowitem}">
                                <span class="fw-semibold">
                                   {{ AppHelper.formatCurrency(rowitem.pre_balance) }}
                                </span>
                            </template>
                            <template v-slot:slotnew_balance="{rowitem}">
                        <span class="fw-semibold">
                              {{
                                AppHelper.formatCurrency(computeBalance(rowitem.pre_balance, rowitem.amount, rowitem.ledger_type))
                            }}
                        </span>
                            </template>

                            <template v-slot:slotstatus="{rowitem}">
                          <span :class="rowitem.status=='F'?'text-danger':rowitem.status=='P'?'Pending':'text-success'">
                            {{ getExt(rowitem.status) }}
                          </span>
                            </template>
                        </elite-grid>
                    </div>
                </div>
            </div>
        </template>


        <template #footer>
            <div class="d-flex justify-content-end align-items-center w-100">


                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" @click="emitClose">
                        <translate>gbl.close</translate>
                    </button>

                </div>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import {computed, onMounted, reactive, ref} from 'vue';
import { Modal } from '@appsbd/vue3-appsbd-libs'
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import appsbdUtls from "@/libs/AppsbdUtls.js";
import {useLedgerStore} from "@/modules/AdminPanel/Ledger/LeadgerStore.js";

import AppsbdUtls from "@/libs/AppsbdUtls.js";

import cities from "@/libs/cities.js";
import AppHelper from "../../../libs/AppHelper.js";
import ContactNumberInput from "@/components/ContactNumberInput.vue";
import LedgerAmountFormat from "@/components/LedgerAmountFormat.vue";


const ledgerStore=useLedgerStore();
const props = defineProps({
    id: {
        default: null
    }
})
const emit = defineEmits(['close', 'reload'])


const ledgerModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)
const searchProps = ref([])
const sortProps = ref(null)
const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const data_column = [

    EliteColumnModel.getColumn({ name: 'invoice_number', title: 'gbl.invoice.number', width: '200px', is_sortable: true }),

    EliteColumnModel.getColumn({ name: 'transaction_date', title: 'gbl.transaction.date', width: '200px', is_sortable: false, }),

    EliteColumnModel.getColumn({ name: 'pre_balance', title: 'gbl.pre.balance', width: '150px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'amount', title: 'gbl.amount', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'new_balance', title: 'gbl.current.balance', width: '150px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'status', title: 'gbl.status', width: '100px', is_sortable: false }),

];

async function loadGridData() {
    isShowLoader.value = true
    const param = new APBDRequestParam()
    param.limit = gridData.limit
    param.page = gridData.page
    param.customer_id = props.id
    for (const item of searchProps.value) {
        param.AddSrcItem(item.propName, item.value, item.operators)
    }
    if (sortProps.value) {
        param.AddSortItem(sortProps.value.prop, sortProps.value.ord)
    }
    try {
        const response = await ledgerStore.getData(param)
        if (response) {
            gridData.page = response.page
            gridData.limit = response.limit
            gridData.records = response.records
            gridData.total = response.total
            gridData.rowdata = response.rowdata
        }
    } catch (e) {
        console.error(e)
    }
    isShowLoader.value = false
}

function searchData(data) {
    searchProps.value = data
    gridData.page = 1
    loadGridData()
}
function clearSearch() {
    searchProps.value = []
    loadGridData()
}

function refreshGrid() {
    loadGridData()
}

function eliteGridLoadData(gparam) {
    gridData.limit = gparam.limit
    gridData.page = gparam.page
    sortProps.value = gparam.sort_prop ? { prop: gparam.sort_prop, ord: gparam.sort_ord } : null
    loadGridData()
}
function computeBalance(prevBalance = 0, amount = 0, type = 'C') {

    const balance = Number(prevBalance)
    const amt = Number(amount)

    console.log(balance);
    console.log("amt : "+amt);




    const newBalance = type === 'D' ? balance - amt : balance + amt
    return Math.round(newBalance * 100) / 100
}

function getType(type) {
    return type === "C" ? "Credit" : "Debit";
}
function getExt(type) {
    return type === "P" ? "Pending" : type === "S" ?"Success":"Failed";
}



const emitClose = () =>  {
    emit('close')
}

const loaderStatusChange = () => {}


onMounted(() => loadGridData())
</script>

<style scoped lang="scss">

</style>
