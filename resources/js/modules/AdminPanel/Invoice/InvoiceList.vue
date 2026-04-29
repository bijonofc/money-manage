<template>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-sm-8">
                        <apbd-filter-panel :filter-options="filterProps" @searchFilter="searchData" @reset="clearSearch" />
                    </div>
                    <div class="col-sm-4  d-flex align-items-center justify-content-end gap-2">
                        <button v-if="$CheckACL('np.invoice-list')" class="btn btn-sm btn-primary" @click="refreshGrid">
                            <i class="apb apb-refresh-ccw-alt"> </i>
                            <span class="ms-2" v-translate>gbl.reload</span>
                        </button>
                        <button v-if="$CheckACL('np.invoice-gen')" class="btn btn-sm btn-primary" @click="openInvModal">
                            <i class="apb apb-circle-plus"> </i>
                            <span class="ms-2" v-translate>gbl.gen.inv</span>
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
                action-width="150px"
                :columns="data_column"
                :show-loader="isShowLoader"
                :show-header="false"
                :grid-data="gridData"
                :show-action-column="true"
                :is-show-row-index-column="true"
                @load-data="eliteGridLoadData"
            >
                <template v-slot:slot-loader>
                    <APBDGridLoader msg="gbl.loading" :msg-params="{name:'invoice'}"/>

                </template>
                <template v-slot:slot-no-record>
                    {{
                        appsbdUtls.translateGettext('No %{type} found', { type: 'invoice' })
                    }}
                </template>

                <template v-slot:slottotal_amount="{rowitem}">
                    {{ appHelper.formatCurrency(rowitem.total_amount) }}
                </template>
                <template v-slot:slotinvoice_date="{rowitem}">
                          <span>
                            {{ appHelper.formatDate(rowitem.invoice_date) }}
                          </span>
                </template>

                <template v-slot:slotdue_date="{ rowitem }">

                    <span :class="rowitem.status=='S'?'text-success':'text-danger'">
                        {{appHelper.formatDate(rowitem.due_date) }}
                    </span>

                </template>
                <template v-slot:slotpaid_at="{ rowitem }">
                    <span class="text-success">
                     {{ appHelper.formatDate(rowitem.paid_at) }}
                    </span>
                </template>

                <template v-slot:slotstatus="{rowitem}">
                          <span :class="rowitem.status=='S'?'text-success':'text-danger'">
                            {{ getStatus(rowitem.status) }}
                          </span>
                </template>


                <template v-slot:actionProperty="slotProps">
                    <div class="d-flex gap-2 justify-content-center">
                        <button v-if="$CheckACL('np.invoice-detail')" class="btn btn-primary btn-sm  d-flex align-items-center" @click="showModal(slotProps.rowitem.id)">
                            <i class="apb apb-edit-01 me-2"></i>
                            <translate>gbl.details</translate>
                        </button>
<!--                        <button v-if="true" class="btn btn-primary btn-sm  d-flex align-items-center" @click="showInvModal(slotProps.rowitem.id)">-->
<!--                        <i class="apb apb-edit-01 me-2"></i>-->
<!--                        <translate>gbl.generate</translate>-->
<!--                    </button>-->

                    </div>

                </template>
            </elite-grid>
        </div>
    </div>
</div>
    <InvoiceDetail :invoice_id="invoiceId" v-if="isShowModal" @close="closeModal" @reload="refreshGrid"  />
    <invoice-gen :invoice_id="invoiceId" v-if="isInvModal" @close="isInvModal=false" @reload="refreshGrid"  />
</template>
<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import {useInvoiceStore} from "@/modules/AdminPanel/Invoice/InvoiceStore.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import AddPackageModal from "@/modules/AdminPanel/Package/AddPackageModal.vue";
import appHelper from "../../../libs/AppHelper.js";
import InvoiceDetail from "@/modules/AdminPanel/Invoice/InvoiceDetail.vue";
import InvoiceGen from "@/modules/AdminPanel/Invoice/InvoiceGen.vue";
import APBDGridLoader from "@/components/APBDGridLoader.vue";
const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader=ref(false)
const isShowModal=ref(false)
const isInvModal=ref(false)
const invoiceId=ref(null)
const invoiceStore=useInvoiceStore();
const data_column = [
    EliteColumnModel.getColumn({ name: 'invoice_number', title: 'gbl.invoice.number', width: '150px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'c_name', title: 'gbl.customer.name', width: '100px', is_sortable: false, }),
    EliteColumnModel.getColumn({ name: 'package_name', title: 'gbl.pkg.name', width: '100px', is_sortable: false, }),
    EliteColumnModel.getColumn({ name: 'total_amount', title: 'gbl.total.price', width: '50px', is_sortable: false,align:'right',title_align:'right' }),
    EliteColumnModel.getColumn({ name: 'invoice_date', title: 'gbl.invoice.date', width: '80px', is_sortable: false,align:'center',title_align:'center' }),
    EliteColumnModel.getColumn({ name: 'due_date', title: 'gbl.due.date', width: '120px', is_sortable: false,align:'center',title_align:'center' }),
    EliteColumnModel.getColumn({ name: 'paid_at', title: 'gbl.paid.at', width: '80px', is_sortable: false,align:'center',title_align:'center' }),
    EliteColumnModel.getColumn({ name: 'status', title: 'gbl.status', width: '50px', is_sortable: false, align:'center',title_align:'center'}),
];
const filterProps = [
    {
        id: 1,
        name: appsbdUtls.translateGettext('Invoice Number'),
        propName: 'invoice_number',
        type: 't',
        options: [],
        operators: 'like',
        value: '',
    },
    {
        id: 2,
        name: appsbdUtls.translateGettext('Customer Name'),
        propName: 'c_name',
        type: 't',
        options: [],
        operators: 'like',
        value: '',
    },
    {
        id: 3,
        name: appsbdUtls.translateGettext('Paid Date'),
        propName: 'paid_at',
        type: 'd',
        options: [],
        operators: 'dt',
        value: ''
    },
    {
        id: 4,
        name: appsbdUtls.translateGettext('Invoice Date'),
        propName: 'invoice_date',
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
        const response = await invoiceStore.getData(param)
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
function openInvModal() {
    isInvModal.value = true
}
function showModal(id) {

    if (id != null) {
        invoiceId.value = id
    }
    isShowModal.value = true
}
function showInvModal(id) {

    if (id != null) {
        invoiceId.value = id
    }
    isInvModal.value = true
}
function closeModal() {
    invoiceId.value = null
    isShowModal.value = false
}

function getStatus(status) {
    return status === "S" ? "Success" : status === "P"?"Pending":status === "F"?"Failed":"Cancelled";
}
function getExt(type) {
    return type === "Y" ? "Yes" : "No";
}
</script>



<style scoped lang="scss">

</style>
