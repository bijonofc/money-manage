<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-xl"
        ref="detailModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >

        <!-- HEADER -->
        <template #header>
            <div class="d-flex align-items-center gap-2 w-100">
                <h6 class="mb-0" v-translate>log.info</h6>
            </div>
        </template>

        <!-- BODY -->
        <template #body>
            <div class="row g-4">

                <!-- Customer Details -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 mt-2">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3" v-translate>gbl.customer.details</h6>

                            <DisplayField label="gbl.customer.name"    :value="transactionData.c_name"/>
                            <DisplayField label="gbl.email"   :value="transactionData.c_email"/>
                            <DisplayField label="gbl.contact.no"   :value="transactionData.c_phone"/>
                            <DisplayField label="gbl.address" :value="transactionData.c_address"/>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3 mt-2">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3" v-translate>gbl.pkg.details</h6>
                            <DisplayField v-if="transactionData.items && transactionData.items.length" label="gbl.pkg.name" :value="transactionData.items[0].item_name"/>
                            <DisplayField v-if="transactionData.items && transactionData.items.length" label="gbl.pkg.price"    :value="transactionData.items[0].unit_price"/>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3 mt-2">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3" v-translate>gbl.trx.details</h6>

                            <DisplayField label="gbl.payment.method" :value="transactionData.payment_method"/>
                            <DisplayField label="gbl.payment.status" :value="transactionData.status=='S'?'Success':'Failed'"/>
                            <DisplayField label="gbl.paid.amount"    :value="transactionData.amount"/>

                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3 mt-2">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3" v-translate>gbl.invoice.details</h6>

                            <DisplayField label="gbl.invoice.no"      :value="transactionData.invoice_uuid"/>
                            <DisplayField label="gbl.customer.ref"    :value="transactionData.customer_ref_id"/>
                            <DisplayField v-if="transactionData.status === 'S'" label="gbl.paid.at"          :value="appHelper.formatDate(transactionData.paid_at)"/>
                            <DisplayField v-if="transactionData.status === 'F'" label="gbl.failed.at"          :value="appHelper.formatDate(transactionData.failed_at)"/>

                        </div>
                    </div>
                </div>

                <!-- Package Details -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3" v-translate>gbl.log</h6>
                            <div>
                                <vue-json-pretty indent="4" showIcon="true" deep="1" :data="transactionData.logs" />
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </template>

        <!-- FOOTER -->
        <template #footer>
            <div class="d-flex justify-content-end w-100">
                <button type="button" class="btn btn-outline-secondary px-4" @click="emitClose">
                    <translate>gbl.close</translate>
                </button>
            </div>
        </template>

    </Modal>
</template>


<script setup>
import {computed, onMounted, reactive, ref,inject} from 'vue';

import { Modal } from '@appsbd/vue3-appsbd-libs'
import appsbdUtls from "@/libs/AppsbdUtls.js";
import {useTransactionStore} from "@/modules/AdminPanel/Transaction/TransactionStore.js";
import DisplayField from "@/components/DisplayField.vue";
import appHelper from "@/libs/AppHelper.js";
import VueJsonPretty from 'vue-json-pretty';
import 'vue-json-pretty/lib/styles.css';


const props = defineProps({
    id: {
        default: null
    }
})
const emit = defineEmits(['close', 'reload'])
const transactionStore = useTransactionStore()
const detailModal = ref(null)
const msg = ref({})
const transactionData = reactive({});

const isShowLoader = ref(false)
const emitClose = () =>  {
    emit('close')
}

const loaderStatusChange = () => {}


const getDetails = async() => {

    if (props.id != null) {
        detailModal.value?.showLoader(true, appsbdUtls.translateGettext('Loading type', { type: 'Transaction Log' }));
        const response = await transactionStore.getLogs({id: props.id})
        if (response.status) {
            Object.assign(transactionData, response.data);
        }
        detailModal.value?.showLoader(false)
    }
}

onMounted(()=>{
    getDetails();
})
</script>

<style scoped lang="scss">
.card {

    &:hover {

        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }
}
.list-group-item {
    font-size: 0.95rem;
    background-color: transparent;
    border: none;
    padding: 0.4rem 0;
    display: flex;
    justify-content: space-between;
}
.rollingLoader{
    position: absolute;
    top: 11px;
    right: 118px;
    display: flex;
    align-items: center;
}
</style>

