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
                        <button v-if="$CheckACL('np.api-add')" class="btn btn-sm btn-primary" @click="openModal">
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
                    <APBDGridLoader msg="gbl.loading" :msg-params="{name:'api'}"/>

                </template>
                <template v-slot:slot-no-record>
                    {{
                        appsbdUtls.translateGettext('No %{type} found', { type: 'api' })
                    }}
                </template>
                <template v-slot:slotapi_key="{rowitem}">
                    <div   class="d-flex align-items-center">
                    <span class="me-2 api-key-text" >
                      {{ rowitem.api_key }}
                    </span>
                    <span style="cursor: pointer;"  v-tooltip="copied[rowitem.id]?appsbdUtls.translateGettext('Copied'):appsbdUtls.translateGettext('Copy')"
                        @click="copyApiKey(rowitem.api_key, rowitem.id)">
                        <i  class="apb apb-check text-success" v-if="copied[rowitem.id]"></i>
                        <i  class="apb apb-file-copy text-primary" v-else></i>
                    </span>
                    </div>
                </template>
                <template v-slot:slotstatus="{rowitem}">
                          <span :class="rowitem.status=='A'?'text-success':'text-danger'" style="cursor: pointer;"
                                @click="changeStatus(rowitem)">
                            {{ appsbdUtls.translateGettext(getStatus(rowitem.status)) }}
                          </span>
                </template>
                <template v-slot:slotcreated_at="{rowitem}">
                          <span>
                            {{ appHelper.formatDate(rowitem.created_at) }}
                          </span>
                </template>
                <template v-slot:slotcreated_ad="{rowitem}">
                          <span>
                            {{ appHelper.formatDate(rowitem.created_at) }}
                          </span>
                </template>
                <template v-slot:actionProperty="slotProps">
                    <div class="d-flex gap-2 justify-content-center">
                        <button v-if="$CheckACL('np.api-update')" class="btn btn-primary btn-sm  d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)">
                            <i class="apb apb-edit-01"></i>
                            <translate class="text-nowrap">gbl.edit.now</translate>
                        </button>

                        <button v-if="$CheckACL('np.api-delete')" class="btn  btn-sm btn-danger  d-flex align-items-center gap-2" @click="deleteApi(slotProps.rowitem.id)">
                            <i class="apb apb-delete-03"></i>
                            <translate class="text-nowrap">gbl.delete</translate>
                        </button>
                    </div>
                </template>
            </elite-grid>
        </div>
    </div>
</div>
    <AddApiModal v-if="isShowModal" :api_id="apiId"  @close="closeModal" @reload="refreshGrid" />
</template>
<script setup>
import { ref, reactive, computed, onMounted,getCurrentInstance } from 'vue'
const { proxy } = getCurrentInstance();
import {useApiStore} from "@/modules/AdminPanel/ApiManage/ApiStore.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import AddPackageModal from "@/modules/AdminPanel/Package/AddPackageModal.vue";
import AddApiModal from "@/modules/AdminPanel/ApiManage/AddApiModal.vue";
import appHelper from "../../../libs/AppHelper.js";
import APBDGridLoader from "@/components/APBDGridLoader.vue";
const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader=ref(false)
const isShowModal=ref(false)
const apiId=ref(null)
const apiStore=useApiStore();
const data_column = [
    EliteColumnModel.getColumn({ name: 'title', title: 'gbl.title', width: '150px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'api_key', title: 'gbl.api.key', width: '150px', is_sortable: false,title_align:'center',align:'center' }),
    EliteColumnModel.getColumn({ name: 'status', title: 'gbl.status', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'host_ip', title: 'gbl.host.ip', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'ref_id', title: 'gbl.ref.id', width: '150px', is_sortable: false, }),
    EliteColumnModel.getColumn({ name: 'created_at', title: 'gbl.created.at', width: '100px', is_sortable: true })
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
        const response = await apiStore.getData(param)
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

const copied = reactive({}) // track copied status per row

function copyApiKey(apiKey, id) {
    navigator.clipboard.writeText(apiKey).then(() => {
        copied[id] = true
        setTimeout(() => {
            copied[id] = false
        }, 1500) // tooltip disappears after 1.5s
    }).catch(err => {
        console.error('Failed to copy: ', err)
    })
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
const changeStatus = async (obj) => {
    if (!obj || !proxy.$CheckACL('np.api-update')) return

    const confirmMsg = obj.status === 'I'
        ? appsbdUtls.translateGettext('gbl.user.active',{name:'api'})
        : appsbdUtls.translateGettext('gbl.user.inactive',{name:'api'})

    appsbdUtls.ShowConfirmRequest(
        confirmMsg,
        async () => {
            try {
                const response = await apiStore.updateApi({
                    id: obj.id,
                    status: obj.status === 'A' ? 'I' : 'A'
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
            confirmButtonText: appsbdUtls.translateGettext('Change'),
            cancelButtonText: appsbdUtls.translateGettext('No'),
            showLoaderOnConfirm: true
        }
    )
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
        apiId.value = id
    }
    isShowModal.value = true
}
function closeModal() {
    apiId.value = null
    isShowModal.value = false
}
function deleteApi(id) {
    const confirmOptions = {
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#2563EB',
        confirmButtonText: appsbdUtls.translateGettext('gbl.delete'),
        cancelButtonText: appsbdUtls.translateGettext('gbl.no'),
        showLoaderOnConfirm: true
    }
    appsbdUtls.ShowConfirmRequest(appsbdUtls.translateGettext('gbl.delete.package',{name:'package'}),
        async () => {
            const resp = await apiStore.deleteApi({ id: id })
            if (resp.status) {
                loadGridData()
            }
            return resp
        },
        confirmOptions
    )
}
function getStatus(sta) {
    return (sta == 'A' ? 'gbl.active' : 'gbl.inactive')
}
</script>



<style lang="scss">
/*    @media (max-width: 768px) {
        .api-key-text {
            text-overflow: ellipsis;
            overflow: hidden;
            width: 50%;
        }
    }*/
</style>
