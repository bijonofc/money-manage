<template>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-sm-8">
                            <apbd-filter-panel :filter-options="filterProps"  @searchFilter="searchData" @reset="clearSearch" />
                        </div>
                        <div class="col-sm-4  d-flex align-items-center justify-content-end gap-2">
                            <button v-if="$CheckACL('np.customer-list')" class="btn btn-sm btn-primary" @click="refreshGrid">
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
                    :show-action-column="true"
                    :is-show-row-index-column="true"
                    @load-data="eliteGridLoadData"
                >
                    <template v-slot:slot-loader>
                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'customer'}"/>

                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            appsbdUtls.translateGettext('No %{type} found', { type: 'customer' })
                        }}
                    </template>
                    <template v-slot:slotprocessing_status="{rowitem}">
                        <div  class="d-flex justify-content-center align-items-center ">
                             <span class="text-nowrap" :class="getProcessingStatusClass(rowitem.processing_status)">
                            {{ getProcessingStatusTitleByKey(rowitem.processing_status) }}
                          </span>
                            <i @click="showLog(rowitem.id)" class="apb apb-report-list3 ms-2 apbd-pointer"></i>
                        </div>

                    </template>
                    <template v-slot:slotstatus="{rowitem}">
                          <span :class="getStatusClass(rowitem.status)">
                            {{ appsbdUtls.translateGettext(getStatus(rowitem.status) )}}
                          </span>
                    </template>
                    <template v-slot:slotcontact_no="{rowitem}">
                        {{AppHelper.toBanglaDigits(rowitem.contact_no,'bn')}}
                    </template>
                    <template v-slot:actionProperty="slotProps" class="apbd-popper">
                        <div class="d-flex align-items-center justify-content-center">
                            <v-dropdown theme="menu" :triggers="['click']" :delay="{show: 0,hide: 100}" >
                                <button class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                                    <translate>gbl.menu</translate>
                                    <i class="apb apb-caret-down"></i>
                                </button>
                                <template #popper>
                                    <ul class="menu-group list-group list-group-flush">
                                        <li class="list-group-item">
                                           <span v-if="$CheckACL('np.ledger-list')" class="apbd-hover d-flex align-items-center gap-2"  v-close-popper @click="showLedger(slotProps.rowitem.id)" style="cursor: pointer">
                                               <i class="apb apb-edit-01"></i>
                                               <translate>gbl.ledger</translate>
                                           </span>
                                        </li>
                                        <li class="list-group-item pointer">
                                            <span v-if="$CheckACL('np.customer-detail')" class="delete-hover d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)" style="cursor: pointer">
                                                 <i class="apb apb-edit-01"></i>
                                                <translate>gbl.view</translate>
                                            </span>
                                        </li>

                                        <li class="list-group-item">
                                            <span class="delete-hover d-flex align-items-center gap-2" @click="visitAdminPanel(slotProps.rowitem.id)" style="cursor: pointer">
                                                <i class="apb apb-edit-01"></i>
                                                <translate>gbl.visit.admin</translate>
                                                 <small-loader v-if="isAnimating[slotProps.rowitem.id]" :show="isAnimating[slotProps.rowitem.id]" size="5"/>
                                            </span>
<!--                                            <AnimatedButton class="btn btn-sm btn-primary" type="button" @click="visitAdminPanel(slotProps.rowitem.id)" :is-animated="isAnimating[slotProps.rowitem.id]">Visit</AnimatedButton>-->
                                        </li>
                                        <li class="list-group-item">
                                            <span class="delete-hover d-flex align-items-center gap-2" @click="visitNogorPanel(slotProps.rowitem.id)" style="cursor: pointer">
                                                <i class="apb apb-edit-01"></i>
                                                <translate>gbl.visit.nogorpos</translate>
                                                <small-loader v-if="isNpBtn[slotProps.rowitem.id]" :show="isNpBtn[slotProps.rowitem.id]" size="5"/>
                                            </span>
                                        </li>
                                    </ul>
                                </template>
                            </v-dropdown>
                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
    <detail-modal v-if="isShowModal" :id="tempId"  @close="closeModal" @reload="refreshGrid" />
    <CustomerBillingModal v-if="isShowBillingModal" :id="tempId"  @close="closeBillingModal" @reload="refreshGrid" />
    <LedgerModal v-if="isShowLedger" :id="customerId"  @close="closeLedger" @reload="refreshGrid" />
    <ProcessingLogModal v-if="isShowlog" :id="tempId" @close="hideLog" />
</template>
<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import {useCustomerStore} from "@/modules/AdminPanel/Customer/CustomerStore.js";
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel,AnimatedButton } from '@appsbd/vue3-appsbd-libs'

import AddPackageModal from "@/modules/AdminPanel/Package/AddPackageModal.vue";
import DetailModal from "@/modules/AdminPanel/Customer/DetailModal.vue";
import LedgerModal from "@/modules/AdminPanel/Customer/LedgerModal.vue";
import SmallLoader from "@/components/SmallLoader.vue";
import {Dropdown as VDropdown,hideAllPoppers} from "floating-vue";
import APBDGridLoader from "@/components/APBDGridLoader.vue";
import AppHelper from "../../../libs/AppHelper.js";
import CustomerBillingModal from "@/modules/AdminPanel/Customer/CustomerBillingModal.vue";
import ProcessingLogModal from "@/modules/AdminPanel/Customer/ProcessingLogModal.vue";

const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})

const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader=ref(false)
const isShowModal=ref(false)
const isShowlog=ref(false)
const isShowBillingModal=ref(false)
const isShowLedger=ref(false)
const tempId=ref(null)
const customerId=ref(null)
const customerStore=useCustomerStore();
const loginStore=useLoginStore();
const data_column = [
    EliteColumnModel.getColumn({ name: 'name', title: 'gbl.name', width: '150px', is_sortable: true }),
    EliteColumnModel.getColumn({ name: 'email', title: 'gbl.email', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'contact_no', title: 'gbl.contact.no', width: '150px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'pkg_name', title: 'gbl.package', width: '100px', is_sortable: false, }),
    EliteColumnModel.getColumn({ name: 'full_domain', title: 'gbl.full.domain', width: '150px', is_sortable: false, }),
    EliteColumnModel.getColumn({ name: 'sub_domain', title: 'gbl.sub.domain', width: '150px', is_sortable: false, }),
    EliteColumnModel.getColumn({ name: 'ref_number', title: 'gbl.ref.number', width: '150px', is_sortable: false,align:'center',title_align:'center' }),
    EliteColumnModel.getColumn({ name: 'processing_status', title: 'gbl.pro.status', width: '50px', is_sortable: false, }),
    EliteColumnModel.getColumn({ name: 'status', title: 'gbl.status', width: '50px', is_sortable: false, }),

];
const filterProps = [
    {
        id: 1,
        name: appsbdUtls.translateGettext('Name'),
        propName: 'name',
        type: 't',
        options: [],
        operators: 'like',
        value: '',
    },
    {
        id: 2,
        name: appsbdUtls.translateGettext('Email'),
        propName: 'email',
        type: 't',
        options: [],
        operators: 'eq',
        value: ''
    },
    {
        id: 3,
        name: appsbdUtls.translateGettext('Phone'),
        propName: 'contact_no',
        type: 't',
        options: [],
        operators: 'eq',
        value: ''
    },
    {
        id: 4,
        name: appsbdUtls.translateGettext('Reference Number'),
        propName: 'ref_number',
        type: 't',
        options: [],
        operators: 'eq',
        value: ''
    },
    {
        id: 5,
        name: appsbdUtls.translateGettext('Full Domain'),
        propName: 'full_domain',
        type: 't',
        options: [],
        operators: 'eq',
        value: ''
    },
    // {
    //     id: 6,
    //     name: 'Package',
    //     propName: 'pkg_id',
    //     type: 'dd',
    //     optionLabel: "name",
    //     optionValueProp: "id",
    //     options: loginStore.packageList,
    //     operators: 'eq',
    //     value: '',
    // },
];

onMounted(() => {
    loadGridData()
})
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
        const response = await customerStore.getData(param)
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
const isAnimating = reactive({})
const isNpBtn = reactive({})

async function visitNogorPanel(id) {
    isNpBtn[id] = true;

    const response = await customerStore.visitNogorPanel(id);
    console.log(response);
    hideAllPoppers();
    isNpBtn[id] = false;

    if (response.status) {
        window.open(response.data.url, '_blank');
    }else {
        appsbdUtls.ShowServerResponseNotification(response.msg);
    }
}
async function visitAdminPanel(id) {
    isAnimating[id] = true;

    const response = await customerStore.visitAdminPanel(id);
    console.log(response);
    hideAllPoppers();
    isAnimating[id] = false;

    if (response.status) {
        window.open(response.data.url, '_blank');
    }else {
        appsbdUtls.ShowServerResponseNotification(response.msg);
    }

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
        tempId.value = id
    }
    isShowModal.value = true
    hideAllPoppers();
}
function showLog(id) {

    if (id != null) {
        tempId.value = id
    }
    isShowlog.value = true
}
function hideLog(id) {
    tempId.value = null
    isShowlog.value = false;
}
function showBillingModal(id) {

    if (id != null) {
        tempId.value = id
    }
    isShowBillingModal.value = true
    hideAllPoppers();
}
function showLedger(id) {
console.log('called');
    if (id != null) {
        customerId.value = id
    }
    isShowLedger.value = true
    hideAllPoppers();
}
function closeModal() {
    tempId.value = null
    isShowModal.value = false
}
function closeBillingModal() {
    tempId.value = null
    isShowBillingModal.value = false
}
function closeLedger() {
    customerId.value = null
    isShowLedger.value = false
}
// function deletePackage(id) {
//     const confirmOptions = {
//         showCancelButton: true,
//         confirmButtonColor: '#dc3545',
//         cancelButtonColor: '#2563EB',
//         confirmButtonText: appsbdUtls.translateGettext('gbl.delete'),
//         cancelButtonText: appsbdUtls.translateGettext('gbl.no'),
//         showLoaderOnConfirm: true
//     }
//     appsbdUtls.ShowConfirmRequest(appsbdUtls.translateGettext('gbl.delete.package',{name:'package'}),
//         async () => {
//             const resp = await packageStore.deletePackage({ package_id: id })
//             if (resp.status) {
//                 loadGridData()
//             }
//             return resp
//         },
//         confirmOptions
//     )
// }
function getStatus(status) {
    return status === "A" ? "gbl.active" : status === "S" ? "gbl.suspended" : status === "D" ? "gbl.due" :  status === "P"?"gbl.processing":"gbl.inactive";
}

function getProcessingStatusTitleByKey(key) {
    const processingStatusMap = {
        "D": appsbdUtls.translateGettext('Database creation'),
        "P": appsbdUtls.translateGettext('Provisioning success'),
        "S": appsbdUtls.translateGettext('Site creation'),
        "C": appsbdUtls.translateGettext('Completed'),
    }
    return processingStatusMap[key] || key;
}

function getStatusClass(status) {
    return status === "A" ? 'text-success': status === "P"? 'text-warning':'text-danger';
}
function getProcessingStatusClass(status) {
    return status === "D" ? 'text-danger': status === "C"? 'text-success':status === "S"?'text-warning':'text-info';
}
function getExt(type) {
    return type === "Y" ? "Yes" : "No";
}
</script>


<style scoped lang="scss">

</style>
