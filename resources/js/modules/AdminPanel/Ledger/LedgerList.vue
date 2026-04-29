<template>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-sm-8">
                            <apbd-filter-panel :is-single="true" @searchFilter="searchData" @reset="clearSearch" />
                        </div>
                        <div class="col-sm-4  d-flex align-items-center justify-content-end gap-2">
                            <button class="btn btn-sm btn-primary" @click="refreshGrid">
                                <i class="apb apb-refresh-ccw-alt"> </i>
                                <span class="ms-2" v-translate>gbl.reload</span>
                            </button>
                            <button v-if="true" class="btn btn-sm btn-primary" @click="openModal">
                                <i class="apb apb-circle-plus"> </i>
                                <span class="ms-2" v-translate>gbl.add.new</span>
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
                    :show-action-column="true"
                    :is-show-row-index-column="true"
                    @load-data="eliteGridLoadData"
                >
                    <template v-slot:slot-loader>
                        <APBDGridLoader msg="packages.loading" />
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            appsbdUtls.translateGettext('No %{type} found', { type: 'Laedger' })
                        }}
                    </template>
                    <template v-slot:slotledger_type="{rowitem}">
                          <span :class="rowitem.ledger_type=='D'?'text-danger':'text-success'">
                            {{ getType(rowitem.ledger_type) }}
                          </span>
                    </template>
                    <template v-slot:slotstatus="{rowitem}">
                          <span :class="rowitem.status=='F'?'text-danger':rowitem.status=='P'?'Pending':'text-success'">
                            {{ getExt(rowitem.status) }}
                          </span>
                    </template>
                    <template v-slot:actionProperty="slotProps">
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="true" class="btn btn-primary btn-sm  d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)">
                                <i class="apb apb-edit-01"></i>
                                <translate>gbl.view</translate>
                            </button>

                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
<!--    <AddPackageModal v-if="isShowModal" :package_id="packageId"  @close="closeModal" @reload="refreshGrid" />-->
</template>
<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import {useLedgerStore} from "@/modules/AdminPanel/Ledger/LeadgerStore.js";

import AddPackageModal from "@/modules/AdminPanel/Package/AddPackageModal.vue";
const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader=ref(false)
const isShowModal=ref(false)
const ledgerId=ref(null)
const ledgerStore=useLedgerStore();
const data_column = [
    EliteColumnModel.getColumn({ name: 'customer_name', title: 'gbl.name', width: '150px', is_sortable: true }),

    EliteColumnModel.getColumn({ name: 'amount', title: 'gbl.amount', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'currency', title: 'gbl.currency', width: '100px', is_sortable: false }),

    EliteColumnModel.getColumn({ name: 'pre_balance', title: 'gbl.pre.balance', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'ledger_type', title: 'gbl.ledger.type', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'status', title: 'gbl.status', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'transaction_date', title: 'gbl.transaction.date', width: '150px', is_sortable: false, }),
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

function openModal() {
    isShowModal.value = true
}
function showModal(id) {

    if (id != null) {
        ledgerId.value = id
    }
    isShowModal.value = true
}
function closeModal() {
    ledgerId.value = null
    isShowModal.value = false
}

function getType(type) {
    return type === "C" ? "Credit" : "Debit";
}
function getExt(type) {
    return type === "P" ? "Pending" : type === "S" ?"Success":"Failed";
}
</script>



<style scoped lang="scss">

</style>

