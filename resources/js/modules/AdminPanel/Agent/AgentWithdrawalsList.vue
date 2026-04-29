<template>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-sm-8">
                            <apbd-filter-panel :filter-options="filterProps" @searchFilter="searchData" @reset="clearSearch"/>
                        </div>
                        <div class="col-sm-4  d-flex align-items-center justify-content-end gap-2">
                            <button v-if="$CheckACL('np.agent-list')" class="btn btn-sm btn-primary"
                                    @click="refreshGrid">
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
                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'withdrawal list'}"/>
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            AppsbdUtls.translateGettext('No %{type} found', {type: 'withdrawal'})
                        }}
                    </template>
                    <template v-slot:slotwithdraw_type="{rowitem}">
                          <span>
                            {{ rowitem.withdraw_type=='B'?'Credit Balance':'Cash' }}
                          </span>
                    </template>

                    <template v-slot:slotmethod="{rowitem}">
                          <span>
                            {{ getMethod(rowitem) }}
                          </span>
                    </template>
                    <template v-slot:slotagent.contact_no="{rowitem}">
                          <span>
                            {{ AppHelper.toBanglaDigits(rowitem.agent.contact_no,'bn') }}
                          </span>
                    </template>
                    <template v-slot:slotamount="{rowitem}">
                        <span>
                            {{ AppHelper.formatCurrency(rowitem.amount) }}
                        </span>
                    </template>

                    <template v-slot:slotstatus="{rowitem}">
                        <span :class="getStatusClass(rowitem.status)" style="cursor: pointer" v-if="rowitem.status !== 'A' && rowitem.status !== 'C' && rowitem.status !== 'D'" @click="convertApproved(rowitem)">
                            {{ getStatus(rowitem.status) }}
                        </span>
                        <span :class="getStatusClass(rowitem.status)" v-else>
                            {{ getStatus(rowitem.status) }}
                        </span>
                    </template>

                    <template v-slot:actionProperty="slotProps">
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="$CheckACL('np.agent-withdrawal-approval')" class="btn btn-primary btn-sm  d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)">
                                <i class="apb apb-report-list5"></i><span v-translate>gbl.details</span>
                            </button>

                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
    <WithdrawalDetailsModal v-if="isShowModal" @close="closeModal" :withdraw_id="withdrawalId" @reload="refreshGrid"/>
    <AgentTopUpModal v-if="isShowTopUpModal" @close="closeModal" :agent="agent" @reload="refreshGrid" />
</template>

<script setup>
import {ref, reactive, computed, onMounted} from 'vue'
import EliteGrid from '@appsbd/vue3-elite-grid'
import {EliteColumnModel} from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import {ApbdFilterPanel} from '@appsbd/vue3-appsbd-libs'
import APBDGridLoader from "@/components/APBDGridLoader.vue";
import {useAgentStore} from "@/modules/AdminPanel/Agent/AgentStore.js";
import AppsbdUtls from "@/libs/AppsbdUtls.js";
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import AgentTopUpModal from "@/modules/AdminPanel/Agent/AgentTopUpModal.vue";
import WithdrawalDetailsModal from "@/modules/AdminPanel/Agent/WithdrawalDetailsModal.vue";
import AppHelper from "@/libs/AppHelper.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";

const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})

const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader = ref(false)
const isShowModal = ref(false)
const isShowTopUpModal = ref(false)

const withdrawalId = ref(null)
const agent = ref(null)
const agentStore = useAgentStore();
const loginStore = useLoginStore();
const data_column = [
    EliteColumnModel.getColumn({name: 'agent.name', title: 'gbl.name', width: '150px', is_sortable: false}),
    EliteColumnModel.getColumn({name: 'agent.contact_no', title: 'gbl.contact.no', width: '150px', is_sortable: false}),
    EliteColumnModel.getColumn({name: 'amount', title: 'gbl.amount', width: '150px',align: 'center',title_align: 'center'}),
    EliteColumnModel.getColumn({name: 'withdraw_type', title: 'gbl.type', width: '100px', is_sortable: false,align: 'center',title_align: 'center'}),
    EliteColumnModel.getColumn({name: 'method', title: 'gbl.method', width: '100px', is_sortable: false,align: 'center',title_align: 'center'}),
    EliteColumnModel.getColumn({name: 'status', title: 'gbl.status', width: '100px', is_sortable: true,align: 'center',title_align: 'center'}),
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
        name: appsbdUtls.translateGettext('Username'),
        propName: 'username',
        type: 't',
        options: [],
        operators: 'like',
        value: '',
    },
    {
        id: 3,
        name: appsbdUtls.translateGettext('Email'),
        propName: 'email',
        type: 't',
        options: [],
        operators: 'eq',
        value: ''
    },
    {
        id: 4,
        name: appsbdUtls.translateGettext('Withdraw Type'),
        propName: 'withdraw_type',
        type: 'dd',
        optionLabel: "name",
        optionValueProp: "val",
        options: [
            {'name':appsbdUtls.translateGettext('Cash'),'val':'C'},
            {'name':appsbdUtls.translateGettext('Credit Balance'),'val':'B'}],
        operators: 'eq',
        value: '',
    },
    {
        id: 5,
        name: appsbdUtls.translateGettext('Method'),
        propName: 'method',
        type: 'dd',
        optionLabel: "name",
        optionValueProp: "val",
        options: [
            {'name':appsbdUtls.translateGettext('MFS'),'val':'M'},
            {'name':appsbdUtls.translateGettext('Banking'),'val':'B'},
            {'name':appsbdUtls.translateGettext('Cash'),'val':'C'},
            {'name':appsbdUtls.translateGettext('Online'),'val':'O'}],
        operators: 'eq',
        value: '',
    },
    {
        id: 6,
        name: appsbdUtls.translateGettext('Status'),
        propName: 'status',
        type: 'dd',
        optionLabel: "name",
        optionValueProp: "val",
        options: [
            {'name':appsbdUtls.translateGettext('Requested'),'val':'R'},
            {'name':appsbdUtls.translateGettext('Processing'),'val':'P'},
            {'name':appsbdUtls.translateGettext('Approved'),'val':'A'},
            {'name':appsbdUtls.translateGettext('Declined'),'val':'D'},
            {'name':appsbdUtls.translateGettext('Cancelled'),'val':'C'}],
        operators: 'eq',
        value: '',
    },
];

onMounted(() => {
    loadGridData();
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
        const response = await agentStore.getAgentWithdrawals(param)
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

async function topUpModal(agentData){
    agent.value = agentData;

    isShowTopUpModal.value=true;
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

function searchData(data) {
    searchProps.value = data
    gridData.page = 1
    loadGridData()
}

function getMethod(item) {
    if (item.withdraw_type=='B')
    {
        return '-';
    }
    switch (item.method) {
        case 'B':
            return appsbdUtls.translateGettext('Bank');
        case 'M':
            return appsbdUtls.translateGettext('MFS');
        case 'O':
            return appsbdUtls.translateGettext('Online');
        case 'C':
            return appsbdUtls.translateGettext('Cash');
        default:
            return 'Unknown';
    }
}
function getStatus(status) {
    switch (status) {
        case 'A':
            return appsbdUtls.translateGettext('Approved');
        case 'P':
            return appsbdUtls.translateGettext('Processing');
        case 'D':
            return appsbdUtls.translateGettext('Declined');
        case 'C':
            return appsbdUtls.translateGettext('Cancel');
        default:
            return appsbdUtls.translateGettext('Requested');
    }
}
function getStatusClass(status) {
    switch (status) {
        case 'A':
            return 'text-success';
        case 'P':
            return 'text-primary';
        case 'D':
            return 'text-danger';
        case 'C':
            return 'text-danger';
        default:
            return 'text-warning';
    }
}

function clearSearch() {
    searchProps.value = []
    loadGridData()
}

function showModal(id) {
    withdrawalId.value = id
    isShowModal.value = true;
}

function closeModal() {
    withdrawalId.value = null;
    agent.value = null;
    isShowModal.value = false;
    isShowTopUpModal.value = false;
}

async function convertApproved(data){
    if(!data.id){
        return;
    }
    AppsbdUtls.ShowConfirmRequest(
        AppsbdUtls.translateGettext('gbl.withdrawal.approved',{name:'agent'}),
        async () => {
            try {
                const response = await agentStore.updateAgentWithdrawals({
                    withdraw_id: data.id,
                    status: 'A'
                })

                if (response.status) {
                    loadGridData()
                }

                return response
            } catch (error) {
                console.error(error)
            }
        },
        {
            showCancelButton: true,
            confirmButtonColor: '#2563EB',
            cancelButtonColor: '#dc3545',
            confirmButtonText: AppsbdUtls.translateGettext('gbl.yes'),
            cancelButtonText: AppsbdUtls.translateGettext('gbl.no'),
            showLoaderOnConfirm: true
        }
    )
}

</script>

<style scoped lang="scss">

</style>
