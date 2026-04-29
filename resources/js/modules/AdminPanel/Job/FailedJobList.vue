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
                            <button v-if="$CheckACL('np.failed-job-list')" class="btn btn-sm btn-primary" @click="refreshGrid">
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
                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'failed.job'}"/>
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            appsbdUtls.translateGettext('No %{type} found', { type: 'failed.job' })
                        }}
                    </template>


                    <template v-slot:slotfailed_at="{ rowitem }">
                        <span>{{ AppHelper.formatDate(rowitem.failed_at) }}</span>
                    </template>


                    <template v-slot:actionProperty="slotProps">
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="$CheckACL('np.failed-job-detail')" class="btn btn-primary btn-sm  d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)">
                                <i class="apb apb-edit-01"></i>
                                <translate>gbl.view</translate>
                            </button>

                            <button v-if="$CheckACL('np.failed-job-delete')" class="btn  btn-sm btn-danger  d-flex align-items-center gap-2" @click="deleteJob(slotProps.rowitem.id)">
                                <i class="apb apb-gear"></i>
                                <translate>gbl.delete</translate>
                            </button>
                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
<!--    <add-contact-modal v-if="isShowModal" :contact_id="jobId"  @close="closeModal" @reload="refreshGrid" />-->
    <!--    <AddPackageModal v-if="isShowModal" :package_id="jobId"  @close="closeModal" @reload="refreshGrid" />-->
    <FailedJobModal v-if="isShowModal" :job_id="jobId" @close="closeModal" />
</template>
<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import {useFailedJobStore} from "@/modules/AdminPanel/Job/FailedJobStore.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import AddPackageModal from "@/modules/AdminPanel/Package/AddPackageModal.vue";
import appHelper from "../../../libs/AppHelper.js";
import AddContactModal from "@/modules/AdminPanel/Contact/AddContactModal.vue";
import APBDGridLoader from "@/components/APBDGridLoader.vue";
import AppHelper from "../../../libs/AppHelper.js";
import FailedJobModal from "@/modules/AdminPanel/Job/FailedJobModal.vue";

const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader=ref(false)
const isShowModal=ref(false)
const jobId=ref(null)
const failedJobStore=useFailedJobStore();
const data_column = [
    EliteColumnModel.getColumn({ name: 'uuid', title: 'gbl.uuid', width: '150px', is_sortable: true }),
    EliteColumnModel.getColumn({ name: 'queue', title: 'gbl.queue', width: '150px', is_sortable: true }),
    EliteColumnModel.getColumn({ name: 'failed_at', title: 'gbl.failed.at', width: '150px', is_sortable: true }),



];
const filterProps = [
    {
        id: 1,
        name: 'Uuid',
        propName: 'uuid',
        type: 't',
        options: [],
        operators: 'like',
        value: '',
    },
    {
        id: 2,
        name: 'Failed At',
        propName: 'failed_at',
        type: 'd',
        options: [],
        operators: 'dt',
        value: ''
    },
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
        const response = await failedJobStore.getData(param)
        if (response.data) {
            gridData.page = response.data.page
            gridData.limit = response.data.limit
            gridData.records = response.data.records
            gridData.total = response.data.total
            gridData.rowdata = response.data.rowdata
        }else {
            appsbdUtls.ShowServerResponseNotification(response.msg)
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
        jobId.value = id
    }
    isShowModal.value = true
}
function closeModal() {
    jobId.value = null
    isShowModal.value = false
}
function deleteJob(id) {
    const confirmOptions = {
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#2563EB',
        confirmButtonText: appsbdUtls.translateGettext('gbl.delete'),
        cancelButtonText: appsbdUtls.translateGettext('gbl.no'),
        showLoaderOnConfirm: true
    }
    appsbdUtls.ShowConfirmRequest(appsbdUtls.translateGettext('gbl.delete.package',{name:'failed.job'}),
        async () => {
            const resp = await failedJobStore.deleteJob({ job_id: id })
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

</style>
