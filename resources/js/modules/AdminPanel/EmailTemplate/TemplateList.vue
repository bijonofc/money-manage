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
                            <button v-if="$CheckACL('np.template-list')" class="btn btn-sm btn-primary" @click="refreshGrid">
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
                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'email.template'}"/>
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            appsbdUtls.translateGettext('No %{type} found', { type: 'email.template' })
                        }}
                    </template>
                    <template v-slot:slotstatus="{rowitem}">
                          <span :class="rowitem.status=='A'?'text-success':'text-danger'" style="cursor: pointer;"
                                @click="changeStatus(rowitem)">
                            {{ appsbdUtls.translateGettext(getStatus(rowitem.status)) }}
                          </span>
                    </template>

                    <template v-slot:actionProperty="slotProps">
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="$CheckACL('np.template-update')"  class="btn btn-sm btn-info d-flex align-items-center gap-2" @click="updateEmailTemplate(slotProps.rowitem.k_word)" >
                                <i class="apb apb-edit-01"></i>
                                <translate>gbl.edit.now</translate>
                            </button>

                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
    <email-template-modal :key_word=" key_word" v-if="showEmailTemplateModal &&  key_word" @close="closeEmailTemplateModal" @reload="loadGridData"/>
</template>
<script setup>
import { ref, reactive, onMounted, computed ,getCurrentInstance} from 'vue'

const { proxy } = getCurrentInstance();
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'


import {useTemplateStore} from '@/modules/AdminPanel/EmailTemplate/TemplateStore.js'
const templateStore=useTemplateStore()
import appsbdUtls from '@/libs/AppsbdUtls.js'

import APBDGridLoader from '@/components/APBDGridLoader.vue'

import EmailTemplateModal from '@/modules/AdminPanel/EmailTemplate/EmailTemplateModal.vue'



const isShowLoader = ref(false)
const isShowModal = ref(false)
const key_word=ref(null)
const showEmailTemplateModal=ref(false)

const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const data_column = [
    EliteColumnModel.getColumn({ name: 'grp',align:'center',title_align:'center',      title: 'gbl.grp', width: '100px', is_group_by: true}),
    EliteColumnModel.getColumn({ name: 'title', align:'center',title_align:'center',  title: appsbdUtls.translateGettext('gbl.title'), width: '150px'}),
    EliteColumnModel.getColumn({ name: 'subject',align:'center',title_align:'center',  title: appsbdUtls.translateGettext('gbl.subject'), width: '300px' }),
    EliteColumnModel.getColumn({ name: 'status', align:'center',title_align:'center',  title: appsbdUtls.translateGettext('gbl.status'), width: '100px'}),


]

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
        const response = await templateStore.getTemplateList(param)
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
function updateEmailTemplate(key) {

    key_word.value = key;
    showEmailTemplateModal.value = true;
}
function closeEmailTemplateModal() {
   key_word.value = null;
   showEmailTemplateModal.value = false;
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



function getStatus(sta) {
    return (sta == 'A' ? 'gbl.active' : 'gbl.inactive')
}

function showModal(id) {
    if (id != null) {
        userId.value = id
    }

    isShowModal.value = true
}
function closeModal() {
    userId.value = null
    isShowModal.value = false
}

const changeStatus = async (obj) => {
    if (!obj) return
    if (!obj || !proxy.$CheckACL('np.template-update')) return

    const confirmMsg = obj.status === 'I'
        ? appsbdUtls.translateGettext('gbl.user.active',{name:'template'})
        : appsbdUtls.translateGettext('gbl.user.inactive',{name:'template'})

    appsbdUtls.ShowConfirmRequest(
        confirmMsg,
        async () => {
            try {
                const response = await templateStore.updateEmailTemplate({
                    k_word: obj.k_word,
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




onMounted(() => loadGridData())

</script>
<style scoped lang="scss">
.apbd-hover{
    font-size: 16px;
    &:hover{
        color:var(--ab-main-color);
    }

}
.badge {
    cursor: pointer;

    &.bg-success {
        &:hover {
            background: var(--ab-main-color, #defedf) !important;
        }
    }

}
</style>

