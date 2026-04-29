<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-lg"
        ref="invoiceModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
      <div>
           <span v-if="props.invoice_id">
                {{AppsbdUtls.translateGettext('gbl.invoice.details')}}
           </span>

      </div>
        </template>

        <template #body>
            <div v-html="invoiceData">
            </div>
<!--            <PdfInvoice :invoice-data="invoiceData" />-->
        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

import {Modal} from '@appsbd/vue3-appsbd-libs'
import appsbdUtls from '@/libs/AppsbdUtls.js'

import AppsbdUtls from '@/libs/AppsbdUtls.js'
import {useInvoiceStore} from "@/modules/AdminPanel/Invoice/InvoiceStore.js";
import PdfInvoice from "@/components/PdfInvoice.vue";

const props = defineProps({
    invoice_id: {
        default: null
    }
})

const emit = defineEmits(['close', 'reload'])

const invoiceStore = useInvoiceStore()


const invoiceModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)
const invoiceData=ref('');



const loaderStatusChange = (v) => {
    isShowLoader.value = v
}

const resetForm = () => {
    invoiceModal.value?.clearForm()
}

// Load if editing
const loadInvoice = async () => {
    if (props.invoice_id != null) {
        invoiceModal.value?.showLoader(true, appsbdUtls.translateGettext('Loading type', { type: 'Invoice' }));
        const response = await invoiceStore.getInvoice({
            invoice_id: props.invoice_id
        })

        if (response.status) {
           invoiceData.value=response.data;
        }
        invoiceModal.value?.showLoader(false)
    }
}





const emitClose = () => {
    emit('close')
}
onMounted(() => loadInvoice());
</script>

<style scoped lang="scss">
/* Add your styles here if needed */
</style>
