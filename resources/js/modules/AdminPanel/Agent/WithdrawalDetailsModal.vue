<template>
    <Modal
        :key="modalKey"
        :modal-msg="msg"
        modal-size="modal-lg"
        ref="withdrawalDetailsModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
            <div class="d-flex align-items-center gap-2 w-100">
                <h6 class="mb-0" v-translate>agent.withdrawal.details</h6>
            </div>
        </template>

        <template #body>
            <div class="row row-cols-1 row-cols-sm-2 g-3">

                <div class="col">
                    <div class="card p-3 h-100">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong>
                                        <translate>gbl.withdrawal.type</translate>
                                        : </strong>
                                    <span
                                        :class="withdrawalData.withdraw_type === 'C' ? 'text-danger' : 'text-success'">
                                    {{ withdrawalData.withdraw_type === 'C' ? 'Cash' : 'Credit Balance' }}
                                </span>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong>
                                        <translate>gbl.withdraw.rq.amount</translate>
                                        : </strong>
                                    <span
                                        :class="withdrawalData.withdraw_type === 'C' ? 'text-danger' : 'text-success'">
                                   ৳ {{ AppHelper.toLocalizedDigits(withdrawalData.amount, 'bn') }}
                                </span>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong>
                                        <translate>gbl.current.balance</translate>
                                        : </strong>
                                    <span class="text-success">৳ {{
                                            AppHelper.toLocalizedDigits(withdrawalData.agent.agent_com_amount - withdrawalData.agent.com_withdrawn_amount, 'bn')
                                        }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <strong style="white-space: nowrap">
                                        <translate>gbl.note</translate>
                                        : </strong>
                                    <p class="p-0 m-0 text-end">{{withdrawalData.note}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card p-3 h-100">
                        <div class="row">
                            <div class="col-12 mb-2" v-if="withdrawalData.withdraw_type == 'C'">
                                <div class="d-flex justify-content-between">
                                    <strong>
                                        <translate>{{ withdrawlMethodTitle }}</translate>
                                        : </strong>
                                    <span class="text-success">{{ withdrawalData.ref_no }}</span>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong>
                                        <translate>gbl.req.at</translate>
                                        : </strong>
                                    <span>{{ AppHelper.formatDate(withdrawalData.requested_at) }}</span>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="col-form-label p-0" for="status">
                                        <strong>
                                            <translate>gbl.status</translate>
                                        </strong>
                                    </label>
                                    <div class="w-75">
                                        <div class="d-flex justify-content-end gap-2" v-if="['D', 'C' ,'A'].includes(withdrawalData.status) && !isEditingField">
                                            <span :class="withdrawalData.status === 'D'|| withdrawalData.status === 'C' ? 'text-danger' : 'text-success'">{{ getStatus(withdrawalData.status) }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2"  v-if="['R', 'P' ].includes(withdrawalData.status) || isEditingField">
                                            <Field
                                                id="status" rules="required"
                                                name="withdrawalStatus"
                                                label="Status"
                                                v-model="withdrawalData.status">
                                                <multiselect
                                                    id="status"
                                                    class="multiselect-sm"
                                                    v-model="withdrawalData.status"
                                                    :options="statusList"
                                                    @change="setEditable(true)"
                                                    label="title"
                                                    valueProp="val"
                                                    :searchable="true"
                                                />
                                            </Field>
                                        </div>


                                        <ErrorMessage name="withdrawalStatus" class="apbd-v-error"/>
                                    </div>
                                </div>

                            </div>
                            <div class="col-12 mb-2" >
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <label class="col-form-label p-0" for="status">
                                        <strong>
                                            <translate>gbl.remark</translate>
                                        </strong>
                                    </label>
                                    <div class="w-100" >
                                        <Field
                                            class="w-100 remark-content"
                                            name="remarks"
                                            :rules="remarkRequiresCon?'required':''"
                                            v-slot="{ field, errors }"
                                            v-model="withdrawalData.remarks"
                                            as="textarea"
                                        >
                                        </Field>
                                        <ErrorMessage name="remarks" class="apbd-v-error"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </template>

        <template #footer>
            <button type="button" class="btn btn-outline-secondary px-4" @click="emitClose">
                <translate>gbl.close</translate>
            </button>
            <button type="submit" v-if=" $CheckACL('np.agent-withdrawal') && ['R', 'P' ].includes(withdrawalData.status) || isEditingField"
                    class="btn btn-success px-4 d-flex align-items-center gap-2">
                <translate>gbl.submit</translate>
                <small-loader v-if="isALoading"  color="#ece" size="5"/>
            </button>
        </template>
    </Modal>
</template>

<script setup>
import {ref, reactive, watch, computed, onMounted, getCurrentInstance} from 'vue'

const {proxy} = getCurrentInstance();
import {Modal} from '@appsbd/vue3-appsbd-libs'
import AppHelper from "../../../libs/AppHelper.js";
import {useAgentStore} from "@/modules/AdminPanel/Agent/AgentStore.js";
import Multiselect from "@vueform/multiselect";
import {ErrorMessage, Field} from "vee-validate";
import SmallLoader from "@/components/SmallLoader.vue";
import AppsbdUtls from "@/libs/AppsbdUtls.js";

const props = defineProps({
    withdraw_id: {default: null}
})

const emit = defineEmits(['close', 'reload'])
const agentStore = useAgentStore()
const withdrawalData = reactive(
    {
        id: null,
        agent_id: null,          // bigint unsigned
        method: 'B',             // M=MFS, B=Bank, O=Online, C=Cash
        withdraw_type: 'C',      // C=Cash, B=Balance
        ref_no: '',              // reference number
        amount: 0,               // decimal(12,2)
        fee: 0,                  // decimal(12,2)
        net_amount: 0,           // amount - fee
        currency: 'BDT',         // default BDT
        status: 'R',             // R=Requested
        requested_at: '',        // timestamp (ISO string / datetime)
        processed_at: null,      // timestamp | null
        approved_by: 0,          // bigint unsigned
        remarks: '',             // rejected/cancel note
        note: '',                // note from agent
        agent: {},
    }
);
const oldData = reactive({});
const statusList = [
    {title: 'Requested', val: 'R'},
    {title: 'Processing', val: 'P'},
    {title: 'Declined', val: 'D'},
    {title: 'Cancelled', val: 'C'},
    {title: 'Approved', val: 'A'}
];
const withdrawalDetailsModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)
const modalKey = ref(0);
const isEditingField = ref(false);
const isALoading = ref(false);


const withdrawlMethodTitle = computed(() => {
    if (withdrawalData.method === 'M') return 'glb.mtf.number'
    if (withdrawalData.method === 'B') return 'glb.bank.account.number'
    if (withdrawalData.method === 'O') return 'glb.transaction.id'
    if (withdrawalData.method === 'C') return 'glb.cash'
    return 'Unknown'
})


const remarkRequiresCon = computed(() => {
    if (withdrawalData.status == 'D' || withdrawalData.status == 'C') {
        return true;
    }
    return false;
});

const loaderStatusChange = (v) => {
    isShowLoader.value = v
}

const loadingWithdrawalDetail = async () => {
    if (!props.withdraw_id) return
    withdrawalDetailsModal.value?.showLoader(true, AppsbdUtls.translateGettext('Loading Ledger'))

    const response = await agentStore.getWithdrawDetails({withdraw_id: props.withdraw_id})
    if (response.status) {
        Object.assign(withdrawalData, response.data)
        Object.assign(oldData, response.data)
        modalKey.value++
    }


    withdrawalDetailsModal.value?.showLoader(false)
}

const submitForm = async () => {
    try {
        const formData = getFormData();
        if (Object.keys(formData).length === 0) {
            msg.value = {error: ['No Change For Update']}
            withdrawalDetailsModal.value?.showMsgOnly(msg.value, false)
            return
        }
        withdrawalDetailsModal.value?.showLoader(true, AppsbdUtls.translateGettext('Updating withdrawal'))
        const response = await agentStore.updateAgentWithdrawals({
            withdraw_id: props.withdraw_id,
            status: formData.status,
            remarks: formData.remarks
        })
        msg.value = response.msg;
        if (response.status) {
            withdrawalDetailsModal.value.setMessageOnly(response.status);
            emit('reload')
        } else {
            withdrawalDetailsModal.value.showMsgOnly(response.msg, false);
        }
        withdrawalDetailsModal.value.showLoader(false);
    } catch (e) {
        console.log(e);
    }
}

const getFormData = () => {
    if (props.withdraw_id != null) {
        const diff = {}
        for (const key in withdrawalData) {
            if (withdrawalData[key] !== oldData[key]) {
                diff[key] = withdrawalData[key]
            }
        }
        return diff
    }
    return {...withdrawalData}
}


function getStatus(status) {
    switch (status) {
        case 'A':
            return 'Approved';
        case 'P':
            return 'Processing';
        case 'D':
            return 'Declined';
        case 'C':
            return 'Cancel';
        default:
            return 'Requested';
    }
}

function setEditable(val) {
    isEditingField.value = val
}

const emitClose = () => emit('close')

onMounted(() => loadingWithdrawalDetail())
</script>

<style scoped lang="scss">
.text-success {
    font-weight: 600;
}

.text-danger {
    font-weight: 600;
}

.text-primary {
    font-weight: 500;
}

.card {
    border-radius: 0.5rem;
}

.remark-content {
    border: 1px solid #dce1e9;
    font-size: 14px !important;
    font-weight: 400;
    height: 80px;
    border-radius: 5px;

    &:focus, &:focus-visible {
        border: 1px solid #dce1e9;
        outline: unset;
        font-weight: normal;
    }
}
</style>
