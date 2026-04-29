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
                            <button v-if="$CheckACL('np.pusher-list')" class="btn btn-sm btn-primary" @click="refreshGrid">
                                <i class="apb apb-refresh-ccw-alt"> </i>
                                <span class="ms-2" v-translate>gbl.reload</span>
                            </button>
                            <button v-if="$CheckACL('np.pusher-add')" class="btn btn-sm btn-primary" @click="openModal">
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
                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'pusher'}"/>
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            appsbdUtls.translateGettext('No %{type} found', { type: 'pusher' })
                        }}
                    </template>

                    <template v-slot:slotstatus="{rowitem}">
                          <span class="cursor-pointer" :class="rowitem.status=='I'?'text-danger':'text-success'" @click="changeStatus(rowitem)">
                            {{ getExt(rowitem.status) }}
                          </span>
                    </template>



                    <template v-slot:actionProperty="slotProps">
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="$CheckACL('np.pusher-detail')" class="btn btn-primary btn-sm  d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)">
                                <i class="apb apb-edit-01"></i>
                            </button>

                            <button v-if="$CheckACL('np.pusher-delete')" class="btn  btn-sm btn-danger  d-flex align-items-center gap-2" @click="deleteContact(slotProps.rowitem.id)">
                                <i class="apb apb-trash-01"></i>
                            </button>
                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
    <AddPusherModal v-if="isShowModal" :pusher_id="pusherId"  @close="closeModal" @reload="refreshGrid" />
</template>
<script setup>
import { ref, reactive, computed, onMounted,getCurrentInstance } from 'vue'
const { proxy } = getCurrentInstance();
import{usePusherStore} from "@/modules/AdminPanel/Pusher/PusherStore.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import AddPusherModal from "@/modules/AdminPanel/Pusher/AddPusherModal.vue";
import APBDGridLoader from "@/components/APBDGridLoader.vue";
const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader=ref(false)
const isShowModal=ref(false)
const pusherId=ref(null)
const pusherStore=usePusherStore();
const data_column = [
    EliteColumnModel.getColumn({ name: 'title', title: 'gbl.pusher.name', width: '150px' }),
    EliteColumnModel.getColumn({ name: 'push_app_id', title: 'gbl.pusher.app.id', width: '150px' }),
    EliteColumnModel.getColumn({ name: 'push_key', title: 'gbl.pusher.key', width: '150px' }),
    EliteColumnModel.getColumn({ name: 'push_secret', title: 'gbl.secret', width: '150px'}),
    EliteColumnModel.getColumn({ name: 'cluster', title: 'gbl.cluster', width: '150px',align:'center',title_align:'center' }),
    EliteColumnModel.getColumn({ name: 'counter', title: 'gbl.counter', width: '150px',align:'center',title_align:'center', is_sortable: true }),
    EliteColumnModel.getColumn({ name: 'status', title: 'gbl.status', width: '150px',align:'center',title_align:'center', is_sortable: true }),
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
        const response = await pusherStore.getData(param)
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
        pusherId.value = id
    }
    isShowModal.value = true
}
function closeModal() {
    pusherId.value = null
    isShowModal.value = false
}
function deleteContact(id) {
    const confirmOptions = {
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#2563EB',
        confirmButtonText: appsbdUtls.translateGettext('gbl.delete'),
        cancelButtonText: appsbdUtls.translateGettext('gbl.no'),
        showLoaderOnConfirm: true
    }
    appsbdUtls.ShowConfirmRequest(appsbdUtls.translateGettext('gbl.delete.package',{name:'pusher'}),
        async () => {
            const resp = await pusherStore.deleteContact({ contact_id: id })
            if (resp.status) {
                loadGridData()
            }
            return resp
        },
        confirmOptions
    )
}

const changeStatus = async (obj) => {
    if (!obj || !proxy.$CheckACL('np.pusher-update')) return;

    const confirmMsg = obj.status === 'I'
        ? appsbdUtls.translateGettext('gbl.user.active',{name:'pusher'})
        : appsbdUtls.translateGettext('gbl.user.inactive',{name:'pusher'})

    appsbdUtls.ShowConfirmRequest(
        confirmMsg,
        async () => {
            try {
                const response = await pusherStore.updateContact({
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

function getExt(type) {
    return type === "A" ? appsbdUtls.translateGettext("gbl.active") : appsbdUtls.translateGettext("gbl.inactive");
}
</script>



<style scoped lang="scss">

</style>
