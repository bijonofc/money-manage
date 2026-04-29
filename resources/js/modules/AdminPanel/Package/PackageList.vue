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
                        <button v-if="$CheckACL('np.package-list')" class="btn btn-sm btn-primary" @click="refreshGrid">
                            <i class="apb apb-refresh-ccw-alt"> </i>
                            <span class="ms-2" v-translate>gbl.reload</span>
                        </button>
                        <button v-if="$CheckACL('np.package-add')" class="btn btn-sm btn-primary" @click="openModal">
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
                    <APBDGridLoader msg="gbl.loading" :msg-params="{name:'package'}"/>
                </template>
                <template v-slot:slot-no-record>
                    {{
                        appsbdUtls.translateGettext('No %{type} found', { type: 'package' })
                    }}
                </template>
                <template v-slot:slottype="{rowitem}">
                          <span :class="rowitem.type=='M'?'text-danger':'text-success'">
                            {{ getType(rowitem.type) }}
                          </span>
                </template>
                <template v-slot:slotprice="{rowitem}">
                          <span class="text-success">
                            {{ appHelper.toLocalizedDigits(rowitem.price,'bn') }}
                              <translate>gbl.tk</translate>
                          </span>
                </template>
                <template v-slot:slotextended_month="{rowitem}">
                          <span class="text-success">
                            {{ appHelper.toLocalizedDigits(rowitem.extended_month,'bn') }}
                          </span>
                </template>
                <template v-slot:slotis_extended="{rowitem}">
                          <span :class="rowitem.is_extended=='N'?'text-danger':'text-success'">
                            {{ getExt(rowitem.is_extended) }}
                          </span>
                </template>

                <template v-slot:actionProperty="slotProps">
                    <div class="d-flex gap-2 justify-content-center">
                        <button v-if="$CheckACL('np.package-update')" class="btn btn-primary btn-sm  d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)">
                            <i class="apb apb-edit-01"></i>
                            <translate>gbl.edit.now</translate>
                        </button>

                        <button v-if="$CheckACL('np.package-delete')" class="btn  btn-sm btn-danger  d-flex align-items-center gap-2" @click="deletePackage(slotProps.rowitem.id)">
                            <i class="apb apb-gear"></i>
                            <translate>gbl.delete</translate>
                        </button>
                    </div>
                </template>
            </elite-grid>
        </div>
    </div>
</div>
    <AddPackageModal v-if="isShowModal" :package_id="packageId"  @close="closeModal" @reload="refreshGrid" />
</template>
<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import {usePackageStore} from "@/modules/AdminPanel/Package/PackageStore.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import AddPackageModal from "@/modules/AdminPanel/Package/AddPackageModal.vue";
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
const packageId=ref(null)
const packageStore=usePackageStore();
const data_column = [
    EliteColumnModel.getColumn({ name: 'name', title: 'gbl.name', width: '150px', is_sortable: true }),
    EliteColumnModel.getColumn({ name: 'price', title: 'gbl.price', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'extended_month', title: 'gbl.ext.month', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'short_desc', title: 'gbl.short.desc', width: '150px', is_sortable: false, }),
    EliteColumnModel.getColumn({ name: 'type', title: 'gbl.type', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'is_extended', title: 'gbl.is.ext', width: '100px', is_sortable: false })
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
        const response = await packageStore.getData(param)
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
        packageId.value = id
    }
    isShowModal.value = true
}
function closeModal() {
    packageId.value = null
    isShowModal.value = false
}
function deletePackage(id) {
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
            const resp = await packageStore.deletePackage({ package_id: id })
            if (resp.status) {
                loadGridData()
            }
            return resp
        },
        confirmOptions
    )
}
function getType(type) {
    return type === "Y" ? "Yearly" : "Monthly";
}
function getExt(type) {
    return type === "Y" ? "Yes" : "No";
}
</script>



<style scoped lang="scss">

</style>
