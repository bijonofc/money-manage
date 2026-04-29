<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-xl"
        ref="logModal"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >

        <template #header>
            <div class="d-flex align-items-center gap-2 w-100">
                <h6 class="mb-0" v-translate>pro.log</h6>
            </div>
        </template>

        <!-- ================= Body ================= -->
        <template #body>
            <div class="">
                <div class="col-sm-12 mt-3">
                    <EliteGrid
                        :is-rounded="true"
                        :is-group-separate-head="true"
                        action-width="100px"
                        :columns="data_column"
                        :show-loader="isShowLoader"
                        :show-header="false"
                        :grid-data="gridData"
                        :hide-pagination="true"
                        :show-action-column="false"
                        :is-show-row-index-column="false"
                        @load-data="eliteGridLoadData"
                    >
                        <template v-slot:slot-loader>
                            <a-p-b-d-grid-loader msg="loading" />
                        </template>
                        <template v-slot:slot-no-record>
                            {{
                                appsbdUtls.translateGettext('No %{type} found', { type: 'pro.log' })
                            }}
                        </template>
                        <template v-slot:slotcreated_at="{rowitem}">
                            <span>{{ AppHelper.formatDate(rowitem.created_at) }}</span>
                        </template>
                        <template v-slot:slotstatus="{rowitem}">
                                  <span :class="getStatusClass(rowitem.status)">
                                    {{ getStatus(rowitem.status) }}
                                  </span>
                        </template>

                        <template v-slot:slotpre_balance="{rowitem}">
                                <span class="fw-semibold">
                                   {{ AppHelper.formatCurrency(rowitem.pre_balance) }}
                                </span>
                        </template>
                    </EliteGrid>
                </div>
            </div>
        </template>


        <template #footer>
            <div class="d-flex justify-content-end align-items-center w-100">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" @click="emitClose">
                        <translate>gbl.close</translate>
                    </button>
                </div>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import {computed, onMounted, reactive, ref} from 'vue';
import { Modal } from '@appsbd/vue3-appsbd-libs'
import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
import appsbdUtls from "@/libs/AppsbdUtls.js";

import AppsbdUtls from "@/libs/AppsbdUtls.js";

import cities from "@/libs/cities.js";
import AppHelper from "../../../libs/AppHelper.js";
import ContactNumberInput from "@/components/ContactNumberInput.vue";
import LedgerAmountFormat from "@/components/LedgerAmountFormat.vue";
import {useCustomerStore} from "@/modules/AdminPanel/Customer/CustomerStore.js";
import APBDGridLoader from "@/components/APBDGridLoader.vue";


const logStore=useCustomerStore();
const props = defineProps({
    id: {
        default: null
    }
})
const emit = defineEmits(['close', 'reload'])


const logModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)
const searchProps = ref([])
const sortProps = ref(null)
const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const logs = ref([]);
const data_column = [
    EliteColumnModel.getColumn({ name: 'message', title: 'gbl.message', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'created_at', title: 'Create date', width: '200px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'status', title: 'gbl.status', width: '200px', is_sortable: false, }),

];

async function loadGridData() {
    isShowLoader.value = true
    try {
        console.log(props.id);
        const response = await logStore.getProvessioningLog(props.id)
        if (response.status)
        {
            logs.value=response.data;
            gridData.rowdata=response.data;
        }
        console.log(logs);
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
function computeBalance(prevBalance = 0, amount = 0, type = 'C') {

    const balance = Number(prevBalance)
    const amt = Number(amount)

    console.log(balance);
    console.log("amt : "+amt);




    const newBalance = type === 'D' ? balance - amt : balance + amt
    return Math.round(newBalance * 100) / 100
}

function getType(type) {
    return type === "C" ? "Credit" : "Debit";
}
function getStatus(status) {
    return status === "P" ? "Pending" : status === "S" ?"Success":"Failed";
}
function getStatusClass(status) {
    return status === "S" ? "text-success" : status === "F" ? "text-danger" : status === "I" ? "text-info" : status === "W" ? "text-warning" : "";
}



const emitClose = () =>  {
    emit('close')
}

const loaderStatusChange = () => {}


onMounted(() => loadGridData())
</script>

<style scoped lang="scss">

</style>
