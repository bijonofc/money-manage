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

                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'temp.customer'}"/>
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            appsbdUtls.translateGettext('No %{type} found', { type: 'temp.customer' })
                        }}
                    </template>
                    <template v-slot:slotstatus="{rowitem}">
                          <span :class="rowitem.status=='C'?'text-success':rowitem.status=='R'?'text-danger':rowitem.status=='S'?'text-info':'text-warning'">
                            {{ getStatus(rowitem.status) }}
                          </span>
                    </template>
                    <template v-slot:slotcontact_no="{rowitem}">
                        {{AppHelper.toBanglaDigits(rowitem.contact_no,'bn')}}
                    </template>
                    <template v-slot:slotpkg_price="{rowitem}">
                          <span class="text-success">
                            {{ appHelper.toLocalizedDigits(rowitem.pkg_price,'bn') }}
                              <translate>gbl.tk</translate>
                          </span>
                    </template>

                    <template v-slot:actionProperty="slotProps">
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="$CheckACL('np.customer-detail')" class="btn btn-primary btn-sm  d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)">
                                <i class="apb apb-edit-01"></i>
                                <translate>gbl.view</translate>
                            </button>

                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
    <DetailModal v-if="isShowModal" :id="tempId"  @close="closeModal" @reload="refreshGrid" />
</template>
<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import{useTempCustomerStore} from "@/modules/AdminPanel/TempCustomer/TempCustomerStore.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import DetailModal from "@/modules/AdminPanel/TempCustomer/DetailModal.vue";
import AddPackageModal from "@/modules/AdminPanel/Package/AddPackageModal.vue";
import appHelper from "@/libs/AppHelper.js";
import APBDGridLoader from "@/components/APBDGridLoader.vue";
import AppHelper from "@/libs/AppHelper.js";

const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader=ref(false)
const isShowModal=ref(false)
const tempId=ref(null)
const tempStore=useTempCustomerStore();
const data_column = [
    EliteColumnModel.getColumn({ name: 'name', title: 'gbl.name', width: '150px', is_sortable: true }),
    EliteColumnModel.getColumn({ name: 'email', title: 'gbl.email', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'contact_no', title: 'gbl.contact.no', width: '150px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'pkg_price', title: 'gbl.price', width: '100px', is_sortable: false, }),

    EliteColumnModel.getColumn({ name: 'ref_number', title: 'gbl.ref.number', width: '150px', is_sortable: false,align:'center',title_align:'center' }),
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
        const response = await tempStore.getData(param)
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
        tempId.value = id
    }
    isShowModal.value = true
}
function closeModal() {
    tempId.value = null
    isShowModal.value = false
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
    return status === "P" ? "Pending" : status === "S" ? "Submitted" : status === "C" ? "Confirmed":"Rejected";
}
function getExt(type) {
    return type === "Y" ? "Yes" : "No";
}
</script>


<style scoped lang="scss">

</style>
