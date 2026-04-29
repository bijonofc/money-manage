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
                            <button v-if="$CheckACL('np.contact-list')" class="btn btn-sm btn-primary" @click="refreshGrid">
                                <i class="apb apb-refresh-ccw-alt"> </i>
                                <span class="ms-2" v-translate>gbl.reload</span>
                            </button>
                            <button v-if="$CheckACL('np.contact-add')" class="btn btn-sm btn-primary" @click="openModal">
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

                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'gbl.contact'}"/>
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            appsbdUtls.translateGettext('No %{type} found', { type: 'Contact' })
                        }}
                    </template>

                    <template v-slot:slotis_whatsapp="{rowitem}">
                          <span :class="rowitem.is_whatsapp=='N'?'text-danger':'text-success'">
                            {{ getExt(rowitem.is_whatsapp) }}
                          </span>
                    </template>
                    <template v-slot:slotphone="{rowitem}">
                        {{appHelper.toBanglaDigits(rowitem.phone)}}
                    </template>


                    <template v-slot:actionProperty="slotProps">
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="$CheckACL('np.contact-update')" class="btn btn-primary btn-sm  d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)">
                                <i class="apb apb-edit-01"></i>
                                <translate>gbl.edit.now</translate>
                            </button>

                            <button v-if="$CheckACL('np.contact-delete')" class="btn  btn-sm btn-danger  d-flex align-items-center gap-2" @click="deleteContact(slotProps.rowitem.id)">
                                <i class="apb apb-gear"></i>
                                <translate>gbl.delete</translate>
                            </button>
                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
    <add-contact-modal v-if="isShowModal" :contact_id="contactId"  @close="closeModal" @reload="refreshGrid" />
<!--    <AddPackageModal v-if="isShowModal" :package_id="contactId"  @close="closeModal" @reload="refreshGrid" />-->
</template>
<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import{useContactStore} from "@/modules/AdminPanel/Contact/ContactStore.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import AddPackageModal from "@/modules/AdminPanel/Package/AddPackageModal.vue";
import appHelper from "../../../libs/AppHelper.js";
import AddContactModal from "@/modules/AdminPanel/Contact/AddContactModal.vue";
import APBDGridLoader from "@/components/APBDGridLoader.vue";
const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader=ref(false)
const isShowModal=ref(false)
const contactId=ref(null)
const contactStore=useContactStore();
const data_column = [

    EliteColumnModel.getColumn({ name: 'contact_name', title: 'gbl.customer.name', width: '150px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'phone', title: 'gbl.contact.no', width: '150px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'email', title: 'gbl.email', width: '150px', is_sortable: false, }),
    EliteColumnModel.getColumn({ name: 'is_whatsapp', title: 'gbl.whatsapp', width: '150px', is_sortable: false,align: 'center',title_align: 'center' }),


];
const filterProps = [
    {
        id: 1,
        name: 'Contact No',
        propName: 'phone',
        type: 't',
        options: [],
        operators: 'like',
        value: '',
    },
    {
        id: 2,
        name: 'Email',
        propName: 'email',
        type: 't',
        options: [],
        operators: 'eq',
        value: ''
    }
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
        const response = await contactStore.getData(param)
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
        contactId.value = id
    }
    isShowModal.value = true
}
function closeModal() {
    contactId.value = null
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
    appsbdUtls.ShowConfirmRequest(appsbdUtls.translateGettext('gbl.delete.package',{name:'gbl.contact'}),
        async () => {
            const resp = await contactStore.deleteContact({ contact_id: id })
            if (resp.status) {
                loadGridData()
            }
            return resp
        },
        confirmOptions
    )
}

function getExt(type) {
    return type === "Y" ? appsbdUtls.translateGettext("gbl.yes") : appsbdUtls.translateGettext("gbl.no");
}
</script>



<style scoped lang="scss">

</style>
