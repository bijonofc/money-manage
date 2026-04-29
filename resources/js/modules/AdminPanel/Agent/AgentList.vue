<template>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-sm-8">
                            <apbd-filter-panel :filter-options="filterProps" @searchFilter="searchData"
                                               @reset="clearSearch"/>
                        </div>
                        <div class="col-sm-4  d-flex align-items-center justify-content-end gap-2">
                            <button v-if="$CheckACL('np.agent-list')" class="btn btn-sm btn-primary"
                                    @click="refreshGrid">
                                <i class="apb apb-refresh-ccw-alt"> </i>
                                <span class="ms-2" v-translate>gbl.reload</span>
                            </button>
                            <button v-if="$CheckACL('np.agent-add')" class="btn btn-sm btn-primary"
                                    @click="showModal(null)">
                                <i class="apb apb-circle-plus"> </i>
                                <span class="ms-2" v-translate>gbl.add.new</span>
                            </button>

                            <!--                            <button v-if="$CheckACL('np.agent-topup')" class="btn btn-sm btn-success"
                                                                @click="showModal(null)">
                                                            <i class="apb apb-circle-plus"> </i>
                                                            <span class="ms-2" v-translate>gbl.topup</span>
                                                        </button>-->
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

                        <APBDGridLoader msg="gbl.loading" :msg-params="{name:'agent'}"/>
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            appsbdUtls.translateGettext('No %{type} found', {type: 'agent'})
                        }}
                    </template>
                    <template v-slot:slotreferral_code="{ rowitem }">
                        <div class="d-flex align-items-center agent-referral gap-3" :key="rowitem.id">
                            <span>{{ rowitem.referral_code }}</span>

                            <v-dropdown @close-directive="closeEditRef">
                                <button
                                    class="btn btn-sm btn-theme btn-rounded"
                                    type="button"
                                    v-if="rowitem.referral_code"
                                    @click="setReferralForEdit(rowitem)"
                                    v-close-popper.all
                                >
                                    <i class="apb apb-edit-01"></i>
                                </button>

                                <template #popper>
                                    <div class="p-3" style="min-width: 350px !important;">
                                        <Form @submit="editRefCode( rowitem.id)">
                                            <div  style="position:relative;" class="d-flex flex-column">
                                                <response-msg :message="res_msg"/>
                                                <input-field
                                                    label="gbl.referral.code"
                                                    class="form-control form-control-sm"
                                                    containerClass="mb-0"
                                                    rules="required|min:6"
                                                    v-model="ref_vals"
                                                    @update:modelValue="val => checkReferralUnique( rowitem.id)"
                                                    name="edit_referral_code"
                                                    :key="rowitem.id"
                                                >
                                                    <template #prefix>
                                                        AG-
                                                    </template>
                                                </input-field>
                                                <SmallLoader :key="rowitem.id+ref_check_loader" color="#137035"
                                                             style="position:absolute; right: 15px;top: 50px;z-index: 9"
                                                             :show="ref_check_loader"/>
                                                <small class="text-danger" v-if="show_msg" v-translate>gbl.is.ref.exist</small>
                                            </div>
                                            <div class="d-flex gap-2 mt-3">
                                                <animated-button
                                                    class="btn btn-sm btn-success"
                                                    :disabled="!is_ok_ref"
                                                    :is-animated="is_update_referral"
                                                    type="submit"
                                                >
                                                    <translate>gbl.save</translate>
                                                </animated-button>
                                                <button
                                                    class="btn btn-sm btn-danger"
                                                    type="button"
                                                    v-translate
                                                    v-close-popper
                                                >
                                                    gbl.close
                                                </button>
                                            </div>

                                        </Form>


                                    </div>
                                </template>
                            </v-dropdown>
                        </div>
                    </template>

                    <template v-slot:slotstatus="{rowitem}">
                          <span :class="rowitem.status=='A'?'text-success':'text-danger'">
                            {{ rowitem.status=='A'?appsbdUtls.translateGettext('gbl.active'):appsbdUtls.translateGettext('gbl.inactive') }}
                          </span>
                    </template>
                    <template v-slot:slotcontact_no="{rowitem}">
                        {{AppHelper.toBanglaDigits(rowitem.contact_no,'bn')}}
                    </template>

                    <template v-slot:actionProperty="slotProps">

                        <div class="d-flex align-items-center justify-content-center">
                            <VDropdown theme="menu" :triggers="['click']">
                                <button class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                                    <translate>gbl.menu</translate>
                                    <i class="apb apb-caret-down"></i>
                                </button>
                                <template #popper>
                                    <ul class="menu-group list-group list-group-flush">
                                        <li class="list-group-item">
                                           <span v-if="$CheckACL('np.agent-detail')"
                                                 class="apbd-hover d-flex align-items-center gap-2"
                                                 @click="showModal(slotProps.rowitem.id)" style="cursor: pointer">
                                               <i class="apb apb-edit-01"></i>
                                               <translate>gbl.edit.now</translate>
                                           </span>
                                        </li>
                                        <li class="list-group-item pointer">
                                            <span v-if="$CheckACL('np.agent-delete')"
                                                  class="delete-hover d-flex align-items-center gap-2"
                                                  @click="deleteAgentUser(slotProps.rowitem.id)"
                                                  style="cursor: pointer">
                                                 <i class="apb apb-delete-3"></i>
                                               <translate>gbl.delete</translate>
                                            </span>
                                        </li>
                                        <li class="list-group-item" v-if="$CheckACL('np.agent-topup')">
                                            <span class="apbd-hover d-flex align-items-center gap-2"
                                                  @click="topUpModal(slotProps.rowitem)" style="cursor: pointer">
                                                <i class="apb apb-gear"></i>
                                                <translate>gbl.topup</translate>
                                            </span>
                                        </li>
                                    </ul>
                                </template>
                            </VDropdown>
                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
    <AddAgentModal v-if="isShowModal" @close="closeModal" :badge_list="loginStore.badgeList" :agent_id="agentId" @reload="refreshGrid"/>
    <AgentTopUpModal v-if="isShowTopUpModal" @close="closeModal" :agent="agent" @reload="refreshGrid"/>
</template>

<script setup>
import {ref, reactive, computed, onMounted} from 'vue'
import appsbdUtls from "@/libs/AppsbdUtls.js";
import EliteGrid from '@appsbd/vue3-elite-grid'
import {EliteColumnModel} from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import {ApbdFilterPanel, InputField} from '@appsbd/vue3-appsbd-libs'
import APBDGridLoader from "@/components/APBDGridLoader.vue";
import AddAgentModal from "@/modules/AdminPanel/Agent/AddAgentModal.vue";
import {useAgentStore} from "@/modules/AdminPanel/Agent/AgentStore.js";
import AppsbdUtls from "@/libs/AppsbdUtls.js";
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import SmallLoader from "@/components/SmallLoader.vue";
import AgentTopUpModal from "@/modules/AdminPanel/Agent/AgentTopUpModal.vue";
import {Dropdown as VDropdown} from "floating-vue";

import {AppLoader,AnimatedButton} from "@appsbd/vue3-appsbd-libs";
import {ResponseMsg} from "@appsbd/vue3-appsbd-libs";
import {Form} from "vee-validate";
import AppHelper from "../../../libs/AppHelper.js";

const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})

const searchProps = ref([])
const sortProps = ref(null)
const isShowLoader = ref(false)
const isShowModal = ref(false)
const isShowTopUpModal = ref(false)
let ref_vals = reactive('')
const ref_check_loader = ref(false)
const show_msg = ref(false)
const is_ok_ref = ref(false)
const is_update_referral = ref(false)
const res_msg = ref([]);

const agentId = ref(null)
const agent = ref(null)
const agentStore = useAgentStore();
const loginStore = useLoginStore();
const data_column = [
    EliteColumnModel.getColumn({name: 'name', title: 'gbl.name', width: '150px', is_sortable: true}),
    EliteColumnModel.getColumn({name: 'username', title: 'gbl.username', width: '150px', is_sortable: true}),
    EliteColumnModel.getColumn({name: 'email', title: 'gbl.email', width: '100px', is_sortable: false}),
    EliteColumnModel.getColumn({name: 'contact_no', title: 'gbl.contact.no', width: '150px', is_sortable: false}),
    EliteColumnModel.getColumn({name: 'status', title: 'gbl.status', width: '100px', align: 'center', title_align: 'center', is_sortable: false,}),
    EliteColumnModel.getColumn({name: 'referral_code', title: 'gbl.referral.code', width: '200px', is_sortable: false,}),
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
        name: appsbdUtls.translateGettext('Username'),
        propName: 'username',
        type: 't',
        options: [],
        operators: 'like',
        value: '',
    },
    {
        id: 3,
        name:appsbdUtls.translateGettext('Email'),
        propName: 'email',
        type: 't',
        options: [],
        operators: 'eq',
        value: ''
    },
    {
        id: 4,
        name: appsbdUtls.translateGettext('Phone'),
        propName: 'contact_no',
        type: 't',
        options: [],
        operators: 'eq',
        value: ''
    },
];


onMounted(() => {
    loadGridData();
})

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
        const response = await agentStore.getAgents(param)
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

async function topUpModal(agentData) {
    agent.value = agentData;

    isShowTopUpModal.value = true;
}

async function deleteAgentUser(id) {
    try {
        const confirmOptions = {
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#2563EB',
            confirmButtonText: appsbdUtls.translateGettext('gbl.delete'),
            cancelButtonText: appsbdUtls.translateGettext('gbl.no'),
            showLoaderOnConfirm: true
        }
        appsbdUtls.ShowConfirmRequest(AppsbdUtls.translateGettext('gbl.delete.package', {name: 'gbl.agent'}),
            async () => {
                const response = await agentStore.deleteAgent({id: id});
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


function setReferralForEdit(rowitem) {
    if (rowitem.referral_code) {
        ref_vals = rowitem.referral_code.replace(/^AG-/, '');
    }
}

let timer = null;

async function checkReferralUnique(id) {
    if (ref_vals.length < 6) {
        show_msg.value = false;
        is_ok_ref.value = false;
        return;
    }

    ref_check_loader.value = true;
    is_ok_ref.value = false;

    if (timer) {
        clearTimeout(timer);
    }

    timer = setTimeout(async () => {
        try {
            const response = await agentStore.checkUniqueRef({
                id: id,
                referral_code: 'AG-' + ref_vals
            });

            if (response?.status) {
                show_msg.value = false;
                is_ok_ref.value = true;
            } else {
                show_msg.value = true;
                is_ok_ref.value = false;
            }
        } catch (e) {
            console.log(e);
        } finally {
            ref_check_loader.value = false;
        }
    }, 500);
}

async function editRefCode(id) {
    is_update_referral.value = true;
    try {
        const response = await agentStore.updateAgent({id: id, referral_code: 'AG-' + ref_vals});
        if(response){
           res_msg.value = response.msg;
        }
    } catch (e) {
        console.log(e);
    }
    is_update_referral.value = false;
}
function closeEditRef(){
    res_msg.value = [];
    show_msg.value = false;
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
    agentId.value = id
    isShowModal.value = true;
}

function closeModal() {
    agentId.value = null;
    agent.value = null;
    isShowModal.value = false;
    isShowTopUpModal.value = false;
}

</script>

<style scoped lang="scss">

</style>
