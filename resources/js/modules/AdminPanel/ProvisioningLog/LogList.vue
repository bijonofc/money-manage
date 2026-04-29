<template>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-sm-10">
                            <ApbdFilterPanel :is-true="true" :filter-options="filterProps"  @searchFilter="searchData" @reset="clearSearch" />
                        </div>
                        <div class="col-sm-2 d-flex align-items-center justify-content-end gap-2">
                            <button v-if="$CheckACL('np.provisioning-log-list')" class="btn btn-sm btn-primary" @click="refreshGrid">
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
                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'plog'}"/>

                    </template>

                    <template v-slot:slot-no-record>
                        {{
                            AppsbdUtls.translateGettext('No %{type} found', { type: 'plog' })
                        }}
                    </template>
                    <template v-slot:slotstatus="{rowitem}">
                        <span :class="rowitem.status=='S'?'text-success':'text-danger'">
                            {{ getStatus(rowitem.status) }}
                        </span>
                    </template>
                    <template v-slot:slotlog_type="{rowitem}">
                        <span :class="rowitem.log_type=='C'?'text-success':'text-danger'">
                            {{ getLogType(rowitem.log_type) }}
                        </span>
                    </template>
                    <template v-slot:slotcreated_at="{rowitem}">
                        <span :class="rowitem.log_type=='C'?'text-success':'text-danger'">
                            {{ AppHelper.formatDate(rowitem.created_at) }}
                        </span>
                    </template>

                    <template v-slot:actionProperty="slotProps">
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="$CheckACL('np.provisioning-log-detail')" class="btn btn-primary btn-sm  d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)">
                                <i class="apb apb-edit-01"></i>
                                <translate>gbl.details</translate>
                            </button>
                            <button v-if="$CheckACL('np.provisioning-log-delete')" class="btn  btn-sm btn-danger  d-flex align-items-center gap-2" @click="deleteLog(slotProps.rowitem.id)">
                                <i class="apb apb-gear"></i>
                                <translate>gbl.delete</translate>
                            </button>
                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
    <log-modal v-if="isShowModal" :log_id="logId" @close="closeModal" />
</template>
<script setup>
import { ref, reactive, onMounted } from 'vue'
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import {useLogStore} from "@/modules/AdminPanel/ProvisioningLog/LogStore.js";
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import AppsbdUtls from '@/libs/AppsbdUtls.js';
import APBDGridLoader from '@/components/APBDGridLoader.vue';
import appsbdUtls from "@/libs/AppsbdUtls.js";
import AppHelper from "../../../libs/AppHelper.js";
import LogModal from "@/modules/AdminPanel/ProvisioningLog/LogModal.vue";
const logStore=useLogStore();
const loginStore=useLoginStore();
const isShowLoader = ref(false)
const isShowModal = ref(false)
const logId = ref(null)


const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const data_column = [
    EliteColumnModel.getColumn({ name: 'customer_name', title: 'gbl.customer.name', width: '150px' }),
    EliteColumnModel.getColumn({ name: 'message', title: 'gbl.message', width: '400px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'status', title: 'gbl.status', width: '50px', is_sortable: false }),
    // EliteColumnModel.getColumn({ name:  'log_type', title: 'gbl.log.type', width: '100px',  is_sortable: false }),
    // EliteColumnModel.getColumn({ name: 'created_at', title: 'gbl.created.at', width: '100px', is_sortable: true }),
]
const userOptions =  loginStore.customerList.map(user=>({
    label: user.name,
    val: user.id
}));
const filterProps = [
    { id: 1,
        name: appsbdUtls.translateGettext('Customer'),
        propName: 'customer_id',
        placeholder: 'Select customer',
        type: 'dd',
        optionLabel: 'label',
        optionValueProp: 'val',
        options:userOptions,
        operators: 'eq',
        value: '',
    },
    // {
    //     id: 2,
    //     name: 'Create Date',
    //     propName: 'created_at',
    //     type: 'dr',
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
        const response = await logStore.getLogs(param)
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
function showModal(id) {
    if (id != null) {
        logId.value = id
    }
    isShowModal.value = true
}
function closeModal() {
    logId.value = null
    isShowModal.value = false
}
function getStatus(status) {
   if(status=='S') return 'Success';
   if(status=='F') return 'Failed';
   if(status=='I') return 'Info';
   if(status=='W') return 'Warning';
   return 'Unknown';
}
function getLogType(type) {
    if(type=='D') return 'Database creating';
    if(type=='S') return 'Site creating';
    if(type=='P') return 'Provisioning';
    if(type=='C') return 'Completed';
    return 'Unknown';
}
function deleteLog(id) {
    const confirmOptions = {
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#2563EB',
        confirmButtonText: appsbdUtls.translateGettext('gbl.delete'),
        cancelButtonText: appsbdUtls.translateGettext('gbl.no'),
        showLoaderOnConfirm: true
    }
    appsbdUtls.ShowConfirmRequest(appsbdUtls.translateGettext('gbl.delete.package',{name:'log'}),
        async () => {
            const resp = await logStore.deleteLog({ log_id: id })
            if (resp.status) {
                loadGridData()
            }
            return resp
        },
        confirmOptions
    )
}


</script>
<style scoped lang="scss">
.date-range{
    background: yellow;
    .range-input-panel{
        background: red;
        .form-control,.form-control-sm{
            border-top-left-radius: 0px !important;
            border-bottom-left-radius: 0px;
        }
    }
}

</style>
