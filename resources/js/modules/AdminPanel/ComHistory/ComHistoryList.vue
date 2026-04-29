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
                            <button v-if="$CheckACL('np.com-history-list')" class="btn btn-sm btn-primary" @click="refreshGrid">
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
                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'Communication History'}" />
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            appsbdUtls.translateGettext('No %{type} found', { type: 'Communication History' })
                        }}
                    </template>
                    <template v-slot:slottype="{ rowitem }">
                        <span>{{ getTypeText(rowitem.type) }}</span>
                    </template>
                    <template v-slot:slotstatus="{ rowitem }">
                        <span :class="rowitem.status === 'S' ? 'text-success' : 'text-danger'">{{ getStatusText(rowitem.status) }}</span>
                    </template>
                    <template v-slot:slotnext_follow_up="{ rowitem }">
                        <span>{{ AppHelper.formatDate(rowitem.next_follow_up) }}</span>
                    </template>
                    <template v-slot:slotcreated_at="{ rowitem }">
                        <span>{{ AppHelper.formatDate(rowitem.created_at) }}</span>
                    </template>


                    <template v-slot:actionProperty="slotProps">
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="$CheckACL('np.com-history-detail')" class="btn btn-primary btn-sm  d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)">
                                <i class="apb apb-edit-01"></i>
                                <translate>gbl.view</translate>
                            </button>

                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
    <com-history-modal v-if="isShowModal" :com_history_id="comHistoryId" @close="closeModal" @reload="loadGridData" />
    <AddHistoryModal v-if="isShowAddModal" :customer_id="customerId"  @close="closeAddModal" @reload="loadGridData" />
</template>
<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import {useComHistoryStore} from "@/modules/AdminPanel/ComHistory/ComHistoryStore.js";
import AppHelper from "@/libs/AppHelper.js";
import ComHistoryModal from "@/modules/AdminPanel/ComHistory/ComHistoryModal.vue";
import AddHistoryModal from "@/modules/AdminPanel/ComHistory/AddHistoryModal.vue";
import APBDGridLoader from "@/components/APBDGridLoader.vue";
const comHistoryStore = useComHistoryStore();
const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader=ref(false)
const isShowModal=ref(false)
const isShowAddModal=ref(false)
const customerId=ref(null)

const comHistoryId=ref(null)

const data_column = [
    EliteColumnModel.getColumn({ name: 'type', title: 'gbl.com.type', width: '100px', is_sortable: true }),
    EliteColumnModel.getColumn({ name: 'customer_name', title: 'gbl.customer.name', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'creator_name', title: 'gbl.creator.name', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'subject', title: 'gbl.com.subject', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'status', title: 'gbl.status', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'created_at', title: 'gbl.created.at', width: '150px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'next_follow_up', title: 'gbl.next.follow.up', width: '150px', is_sortable: false }),

];
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
        const response = await comHistoryStore.getData(param)
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
    if (id != null) comHistoryId.value = id
    isShowModal.value = true
}
function openModal() {
    isShowAddModal.value = true
}
function closeAddModal() {
    isShowAddModal.value = false
}

function closeModal() {
    comHistoryId.value = null
    isShowModal.value = false
}
onMounted(() => loadGridData())
const TYPE_MAP = {
    C: 'Call',
    S: 'Sms',
    E: 'Email',
    M: 'Meeting',
    O: 'Other'
};

function getTypeText(type) {
    return TYPE_MAP[type] || 'Unknown';
}
const STATUS_MAP = {
    S: 'Success',
    F: 'Failed',
    P: 'Pending'
};

function getStatusText(status) {
    return STATUS_MAP[status] || 'Unknown';
}

</script>


<style scoped lang="scss">

</style>
