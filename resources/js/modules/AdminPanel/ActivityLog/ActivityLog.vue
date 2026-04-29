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
                            <button v-if="$CheckACL('np.activity-list')" class="btn btn-sm btn-primary" @click="refreshGrid">
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
                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'activity.log'}"/>

                    </template>
                    <template v-slot:slotdes="{rowitem}">
                         <span class="d-flex align-items-center">
                               <i :class="getEventIcon(rowitem.event)"></i>
                             <span v-html="makeHtml(AppsbdUtls.translateGettext(rowitem.des,rowitem.des_param))+' '+rowitem.human_time"></span>
                          </span>
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            AppsbdUtls.translateGettext('No %{type} found', { type: 'alog' })
                        }}
                    </template>
                    <template v-slot:slotcreated_at="{ rowitem }">
                        <span>{{ AppHelper.formatDate(rowitem.created_at) }}</span>
                    </template>
                    <template v-slot:actionProperty="slotProps">
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="$CheckACL('np.activity-detail')" class="btn btn-primary btn-sm  d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)">
                                <i class="apb apb-edit-01"></i>
                                <translate>gbl.details</translate>
                            </button>
                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
<ActivityModal v-if="isShowModal" :activity_id="activityId" @close="closeModal" />
</template>
<script setup>
import { ref, reactive, onMounted } from 'vue'
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import {useActivityStore} from './ActivityStore.js'
import AppsbdUtls from '@/libs/AppsbdUtls.js'
import APBDGridLoader from '@/components/APBDGridLoader.vue'
import ActivityModal from '@/modules/AdminPanel/ActivityLog/ActivityModal.vue'
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import AppHelper from "@/libs/AppHelper.js";

const loginStore=useLoginStore();
const settingStore=useActivityStore();
const isShowLoader = ref(false)
const isShowModal = ref(false)
const activityId = ref(null)


const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const data_column = [

    EliteColumnModel.getColumn({ name:  'des', title: 'alog.log',  is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'created_at', title: 'alog.time', width: '200px', is_sortable: true }),
]
const userOptions =  loginStore.customerList.map(user=>({
    label: user.name,
    val: user.id
}));
const filterProps = [
    { id: 1,
        name: 'User',
        propName: 'user_id',
        placeholder: 'Select user',
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
        const response = await settingStore.getLogs(param)
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




function getEventIcon(event) {
    const mapping = {
        created:  { icon: 'apb apb-circle-plus', color: 'text-success' },
        updated:  { icon: 'apb apb-arrow-square-up', color: 'text-warning' },
        deleted:  { icon: 'apb apb-trash-03', color: 'text-danger' },
        login:    { icon: 'apb apb-lock-unlocked-01', color: 'text-primary' },
        logout:    { icon: 'apb apb-lock-03', color: 'text-secondary' },

    }

    const cfg = mapping[event] || { icon: 'apb apb-circle-information', color: 'text-muted' }
    return `${cfg.icon} me-2 fs-6 ${cfg.color}`
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
        activityId.value = id
    }
    isShowModal.value = true
}
function closeModal() {
    activityId.value = null
    isShowModal.value = false
}

function makeHtml(text){


        // Parse headers (optional enhancement)
        text = text.replace(/^# (.*$)/gim, '<h1>$1</h1>');
        text = text.replace(/^## (.*$)/gim, '<h2>$1</h2>');
        text = text.replace(/^### (.*$)/gim, '<h3>$1</h3>');

        // Parse bold text
        text = text.replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>');

        // Parse italic text
        text = text.replace(/\*(.*?)\*/gim, '<em>$1</em>');

        // Parse color tags
        text = text.replace(/\[color:(.*?)\](.*?)\[\/color\]/gim, '<span style="color: $1">$2</span>');

        // Parse line breaks
        text = text.replace(/\n/gim, '<br>');

      return text;

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
