<template>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-sm-8">
                            <apbd-filter-panel :is-single="true" @searchFilter="searchData" @reset="clearSearch"/>
                        </div>
                        <div class="col-sm-4  d-flex align-items-center justify-content-end gap-2">
                            <button v-if="$CheckACL('np.agent-badge')" class="btn btn-sm btn-primary"
                                    @click="refreshGrid">
                                <i class="apb apb-refresh-ccw-alt"> </i>
                                <span class="ms-2" v-translate>gbl.reload</span>
                            </button>
                            <button v-if="$CheckACL('np.agent-badge-add')" class="btn btn-sm btn-primary"
                                    @click="showModal(null)">
                                <i class="apb apb-circle-plus"> </i>
                                <span class="ms-2" v-translate>gbl.add.new</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
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

                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'badge'}"/>
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            appsbdUtls.translateGettext('No %{type} found', {type: 'badge'})
                        }}
                    </template>
                    <template v-slot:slotmin_acc_open="{rowitem}">
                        {{
                            appHelper.toLocalizedDigits(rowitem.min_acc_open)
                        }}
                    </template>
                    <template v-slot:slotcom_amount="{rowitem}">
                        {{
                            appHelper.toLocalizedDigits(rowitem.com_amount)
                        }}
                    </template>
                    <template v-slot:slotcus_dis_amount="{rowitem}">
                        {{
                            appHelper.toLocalizedDigits(rowitem.cus_dis_amount)
                        }}
                    </template>
                    <template v-slot:slotcus_dis_type="{rowitem}">
                          <span>
                            {{ rowitem.cus_dis_type == 'P' ? appsbdUtls.translateGettext('gbl.percentage') : appsbdUtls.translateGettext('gb.fixed') }}
                          </span>
                    </template>
                    <template v-slot:slotcom_type="{rowitem}">
                          <span>
                            {{ rowitem.com_type == 'P' ? appsbdUtls.translateGettext('gbl.percentage') : appsbdUtls.translateGettext('gb.fixed') }}
                          </span>
                    </template>

                    <template v-slot:actionProperty="slotProps">
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="$CheckACL('np.agent-badge-detail')"
                                    class="btn btn-primary btn-sm  d-flex align-items-center gap-2"
                                    @click="showModal(slotProps.rowitem.id)">
                                <i class="apb apb-edit-01"></i>
<!--                                <translate>gbl.edit.now</translate>-->
                            </button>
                            <button v-if="$CheckACL('np.agent-badge-delete')"
                                    class="btn  btn-sm btn-danger d-flex align-items-center gap-2"
                                    @click="deleteBadge(slotProps.rowitem.id)">
                                <i class="apb apb-gear"></i>
<!--                                <translate>gbl.delete</translate>-->
                            </button>
                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
    <AddBadgeModal v-if="isShowModal" :badge_id="badgeId" @close="closeModal" @reload="refreshGrid"/>
</template>

<script setup>
import {ApbdFilterPanel} from "@appsbd/vue3-appsbd-libs";
import {ref, reactive, computed, onMounted} from 'vue'
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import {EliteColumnModel} from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import APBDGridLoader from "@/components/APBDGridLoader.vue";
import {useAgentStore} from "@/modules/AdminPanel/Agent/AgentStore.js";
import AddBadgeModal from "@/modules/AdminPanel/Agent/AddBadgeModal.vue";
import AppsbdUtls from "@/libs/AppsbdUtls.js";
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import appHelper from "../../../libs/AppHelper.js";

const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})

const loginStore = useLoginStore();

const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader = ref(false)
const isShowModal = ref(false)

const badgeId = ref(null)
const agentStore = useAgentStore();
const data_column = [
    EliteColumnModel.getColumn({name: 'title', title: 'gbl.name', width: '150px', is_sortable: true}),
    EliteColumnModel.getColumn({
        name: 'min_acc_open',
        title: 'gbl.minimum.account',
        width: '150px',
        is_sortable: false,
        title_align: 'center',
        align: 'center'
    }),
    EliteColumnModel.getColumn({
        name: 'com_type',
        title: 'gbl.commission.type',
        width: '200px',
        is_sortable: false,
        title_align: 'center',
        align: 'center'
    }),
    EliteColumnModel.getColumn({
        name: 'com_amount',
        title: 'gbl.com',
        width: '150px',
        is_sortable: false,
        title_align: 'center',
        align: 'center'
    }),
    EliteColumnModel.getColumn({
        name: 'cus_dis_type',
        title: 'gbl.cus.dis.type',
        width: '200px',
        is_sortable: false,
        title_align: 'center',
        align: 'center'
    }),
    EliteColumnModel.getColumn({
        name: 'cus_dis_amount',
        title: 'gbl.cus.dis.amount',
        width: '200px',
        is_sortable: false,
        title_align: 'center',
        align: 'center'
    }),
];

onMounted(() => {
    loadGridData()
})

async function deleteBadge(id) {
    try {
        const confirmOptions = {
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#2563EB',
            confirmButtonText: appsbdUtls.translateGettext('gbl.delete'),
            cancelButtonText: appsbdUtls.translateGettext('gbl.no'),
            showLoaderOnConfirm: true
        }
        appsbdUtls.ShowConfirmRequest(AppsbdUtls.translateGettext('gbl.delete.package', {name: 'gbl.badge'}),
            async () => {
                const response = await agentStore.deleteBadge({id: id});
                if (response.status) {
                    loadGridData();
                }
                return response;
            },
            confirmOptions
        );
    } catch (e) {
        console.log(e);
    }
}

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
        const response = await agentStore.getAgentBadges(param)
        if (response) {
            gridData.page = response.page
            gridData.limit = response.limit
            gridData.records = response.records
            gridData.total = response.total
            gridData.rowdata = response.rowdata
            loginStore.badgeList = [...response.rowdata]
        }
    } catch (e) {
        console.error(e)
    }
    isShowLoader.value = false
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

function clearSearch() {
    searchProps.value = []
    loadGridData()
}

function showModal(id) {
    badgeId.value = id;
    isShowModal.value = true;

}

function closeModal() {
    badgeId.value = null;
    isShowModal.value = false;
}
</script>

<style scoped lang="scss">

</style>
