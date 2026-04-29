<template>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-sm-8">
                            <apbd-filter-panel :is-true="true" :filter-options="filterProps" :isSearchable="true"  @searchFilter="searchData" @reset="clearSearch" />
                        </div>
                        <div class="col-sm-4  d-flex align-items-center justify-content-end gap-2">
                            <button  v-if="$CheckACL('np.payment-list')" class="btn btn-sm btn-primary" @click="refreshGrid">
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
                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'payment'}"/>
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            appsbdUtls.translateGettext('No %{type} found', { type: 'payment' })
                        }}
                    </template>
                    <template v-slot:slottype="{rowitem}">
                          <span :class="rowitem.type=='P'?'text-danger':'text-success'">
                            {{ getType(rowitem.type) }}
                          </span>
                    </template>
                    <template v-slot:slotis_confirmed="{rowitem}">
                          <span :class="rowitem.is_confirmed=='N'?'text-danger':'text-success'">
                            {{ getExt(rowitem.is_confirmed) }}
                          </span>
                    </template>
                    <template v-slot:slotref_type="{rowitem}">
                          <span :class="rowitem.ref_type=='N'?'text-danger':'text-success'">
                            {{ getMethod(rowitem.ref_type) }}
                          </span>
                    </template>
                    <template v-slot:slottotal_amount="{rowitem}">
<!--                        {{ AppHelper.(rowitem.total_amount) }}-->
                        {{AppHelper.formatCurrency(rowitem.total_amount)}}
                    </template>
                    <template v-slot:slotconfirmed_at="{rowitem}">
                          <span :class="rowitem.is_confirmed=='N'?'text-danger':'text-success'">
                            {{ AppHelper.formatDate(rowitem.confirmed_at) }}
                          </span>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>

</template>
<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import {usePaymentStore} from "@/modules/AdminPanel/PaymentRef/PaymentStore.js";
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import AppHelper from "../../../libs/AppHelper.js";
import APBDGridLoader from "@/components/APBDGridLoader.vue";
const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader=ref(false)
const isShowModal=ref(false)
const ledgerId=ref(null)
const paymentStore=usePaymentStore();
const loginStore=useLoginStore();
const userOptions =  loginStore.customerList.map(user=>({
    label: user.name,
    val: user.id
}));
const filterProps = [
    { id: 1,
        name: 'User',
        propName: 'customer_id',
        placeholder: 'Select user',
        type: 'dd',
        optionLabel: 'label',
        optionValueProp: 'val',
        options:userOptions,
        operators: 'eq',
        value: '',
    },
    { id: 2,
        name: 'Invoice',
        propName: 'invoice_number',
        options: [],
        type: 't',
        operators: 'eq',
        value: '',
    },
    { id: 3,
        name: 'Transaction ID',
        propName: 'trx_id',
        options: [],
        type: 't',
        operators: 'eq',
        value: '',
    },
];

const data_column = [
    EliteColumnModel.getColumn({ name: 'customer_name', title: 'gbl.name', width: '150px', is_sortable: true }),
    EliteColumnModel.getColumn({ name: 'invoice_number', title: 'gbl.inv.number', width: '150px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'trx_id', title: 'gbl.trx.id', width: '150px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'total_amount', title: 'gbl.price', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'payment_method', title: 'gbl.ref.method', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'confirmed_at', title: 'gbl.confirm.at', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'is_confirmed', title: 'gbl.is.confirm', width: '150px',align:'center',title_align:'center', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'type', title: 'gbl.status', width: '100px', is_sortable: false }),



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
        const response = await paymentStore.getData(param)
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
    return type === "P" ? "Pending" : "Confirmed";
}
function getExt(type) {
    return type === "Y" ? appsbdUtls.translateGettext("gbl.yes") : appsbdUtls.translateGettext("gbl.no");
}
function getMethod(type) {
    return type === "B" ? "bKash" : "Nogod";
}
</script>



<style scoped lang="scss">

</style>

