<template>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-sm-8">
                            <apbd-filter-panel :filter-options="filterProps" @searchFilter="searchData" @reset="clearSearch" />
<!--                            <apbd-filter-panel :is-single="true" @searchFilter="searchData" @reset="clearSearch"/>-->
                        </div>
                        <div class="col-sm-4 d-flex flex-wrap align-items-sm-center align-items-start justify-content-start justify-content-sm-end gap-2">

                            <button v-if="$CheckACL('np.company-ledger-add-income')" type="button"
                                    class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                    @click="showExpenseModal('income')">
                                <i class="apb apb-circle-minus me-1"></i>
                                <translate>gbl.add.income</translate>
                            </button>


                            <button v-if="$CheckACL('np.company-ledger-add-expense')" type="button"
                                    class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                    @click="showExpenseModal('expense')">
                                <i class="apb apb-circle-plus me-1"></i>
                                <translate>gbl.add.expense</translate>
                            </button>


                            <button v-if="$CheckACL('np.company-ledger-list')" class="btn btn-sm btn-primary"
                                    @click="refreshGrid">
                                <i class="apb apb-refresh-ccw-alt"> </i>
                                <span class="ms-2" v-translate>gbl.reload</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 mt-3">
            <elite-grid
                :is-rounded="true"
                :is-group-separate-head="true"
                action-width="100px"
                :columns="data_column"
                :show-loader="isShowLoader"
                :show-header="false"
                :grid-data="gridData"
                :show-action-column="true"
                :is-show-row-index-column="true"
                @load-data="eliteGridLoadData"
            >
                <template v-slot:slot-loader>
                    <APBDGridLoader msg="gbl.loading" :msg-params="{name:'ledger'}"/>

                </template>
                <template v-slot:slot-no-record>
                    {{
                        appsbdUtls.translateGettext('No %{type} found', {type: 'ledger'})
                    }}
                </template>
                <template v-slot:slottitle="{rowitem}">
                     <span class=""
                           v-html="appsbdUtls.makeHtml(AppsbdUtls.translateGettext(rowitem.title,rowitem.title_params))">
                      </span>
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
                        <span class="text-success fw-semibold">
                           {{ AppHelper.formatCurrency(rowitem.pre_balance) }}
                        </span>
                </template>
                <template v-slot:slotnew_balance="{rowitem}">
                        <span class="text-success fw-semibold">
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
                <template v-slot:actionProperty="slotProps">
                    <div class="d-flex gap-2 justify-content-center">
                        <button v-if="$CheckACL('np.company-ledger-detail')"
                                class="btn btn-primary btn-sm  d-flex align-items-center gap-2"
                                @click="showModal(slotProps.rowitem.id)">
                            <i class="apb apb-edit-01"></i>
                            <translate>view.details</translate>
                        </button>

                    </div>
                </template>
            </elite-grid>
        </div>
        <expense-modal v-if="isShowExpenseModal" :type="actionType" @close="closeExpenseModal" @reload="refreshGrid"/>
        <CompanyLedgerModal v-if="isShowModal" :ledger_id="ledgerId" @close="closeModal" @reload="refreshGrid"/>
    </div>
</template>

<script setup>
import {ref, reactive, onMounted} from 'vue'
import appsbdUtls from '@/libs/AppsbdUtls.js'
import EliteGrid, {EliteColumnModel} from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import {ApbdFilterPanel} from '@appsbd/vue3-appsbd-libs'
import {useCompanyStore} from "@/modules/AdminPanel/Company/CompanyStore.js";
import CompanyLedgerModal from './CompanyLedgerModal.vue'
import AppHelper from '../../../libs/AppHelper.js'
import AppsbdUtls from "@/libs/AppsbdUtls.js";

import ExpenseModal from "@/modules/AdminPanel/Company/ExpenseModal.vue";
import APBDGridLoader from "@/components/APBDGridLoader.vue";
import LedgerAmountFormat from "@/components/LedgerAmountFormat.vue";

const gridData = reactive({page: 1, total: 1, records: 0, limit: 20, rowdata: []})
const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader = ref(false)
const isShowModal = ref(false)
const isShowExpenseModal = ref(false)
const actionType = ref('expense')


const ledgerId = ref(null)
const companyStore = useCompanyStore()

const data_column = [

    EliteColumnModel.getColumn({name: 'title', title: 'gbl.title', width: '250px', is_sortable: true}),
    EliteColumnModel.getColumn({name: 'amount', title: 'gbl.amount', width: '80px', is_sortable: false}),
    EliteColumnModel.getColumn({name: 'pre_balance', title: 'gbl.pre.balance', width: '80px', is_sortable: false}),
    EliteColumnModel.getColumn({name: 'new_balance', title: 'gbl.current.balance', width: '80px', is_sortable: false}),
    EliteColumnModel.getColumn({
        name: 'transaction_date',
        title: 'gbl.transaction.date',
        width: '150px',
        is_sortable: false
    }),
]
const filterProps = [
    {
        id: 1,
        name: appsbdUtls.translateGettext('Ledger Type'),
        propName: 'ledger_type',
        placeholder: 'Enter value',
        type: 'dd',
        optionLabel: "label",
        optionValueProp: "val",
        options: [
            {label: appsbdUtls.translateGettext("Credit"), val: "C"},
            {label: appsbdUtls.translateGettext("Debit"), val: "D"}
        ],
        operators: 'eq',
        value: '',
    },
    {
        id: 2,
        name: appsbdUtls.translateGettext('Transaction Date'),
        propName: 'transaction_date',
        type: 'd',
        options: [],
        operators: 'dt',
        value: ''
    },
    // {
    //     id: 3,
    //     name: 'Date Between',
    //     propName: 'order_date',
    //     type: 'dr',
    //     options: [],
    //     operators: 'bt',
    //     value: {
    //         start: '',
    //         end: ''
    //     }
    // }
];
onMounted(() => loadGridData())

async function loadGridData() {
    isShowLoader.value = true
    const param = new APBDRequestParam()
    param.limit = gridData.limit
    param.page = gridData.page
    for (const item of searchProps.value) {
        param.AddSrcItem(item.propName, item.value, item.operators)
    }
    if (sortProps.value) {
        param.AddSortItem(sortProps.value.prop, sortProps.value.ord)
    }
    try {
        const response = await companyStore.getData(param)
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

function computeBalance(prevBalance = 0, amount = 0, type = 'C') {

    const balance = Number(prevBalance)
    const amt = Number(amount)
    if (!isFinite(balance) || !isFinite(amt)) return 0
    const newBalance = type === 'D' ? balance - amt : balance + amt
    return Math.round(newBalance * 100) / 100
}

function refreshGrid() {
    loadGridData()
}

function eliteGridLoadData(gparam) {
    gridData.limit = gparam.limit
    gridData.page = gparam.page
    sortProps.value = gparam.sort_prop ? {prop: gparam.sort_prop, ord: gparam.sort_ord} : null
    loadGridData()
}

function showModal(id) {
    if (id != null) ledgerId.value = id
    isShowModal.value = true
}

function showExpenseModal(type) {
    actionType.value = type
    isShowExpenseModal.value = true
}

function closeExpenseModal() {
    isShowExpenseModal.value = false
}


function closeModal() {
    ledgerId.value = null
    isShowModal.value = false
}

function getType(type) {
    return type === 'C' ? 'Credit' : 'Debit'
}


</script>

<style scoped lang="scss">
</style>
