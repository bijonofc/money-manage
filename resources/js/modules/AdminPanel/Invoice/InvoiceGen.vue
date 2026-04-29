<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-md"
        ref="invoiceModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
      <div>
           <span>
                {{AppsbdUtls.translateGettext('gbl.gen.inv')}}
           </span>

      </div>
        </template>

        <template #body>
           <div class="row">
               <div class="col-md-6">
                   <apbd-dropdown label="gbl.customer.id" class="form-control form-control-sm" name="customer_id" v-model="invoiceData.customer_id" rules="required" :options="dropdownCustomers" placeholder="Select" />
               </div>
              <div class="col-md-6">
                  <apbd-dropdown label="gbl.pkg.id" class="form-control form-control-sm" name="package_id" v-model="invoiceData.package_id" rules="required" :options="dropdownPackages" placeholder="Select" />
              </div>
           </div>
        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
            <button class="btn btn-sm btn-primary" type="submit" v-translate>
                gbl.generate
            </button>
        </template>
    </Modal>
</template>

<script setup>
import {ref, reactive, onMounted, computed} from 'vue'
import {Field, ErrorMessage} from 'vee-validate'
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";

import {ApbdDropdown, Modal} from '@appsbd/vue3-appsbd-libs'
import appsbdUtls from '@/libs/AppsbdUtls.js'

import AppsbdUtls from '@/libs/AppsbdUtls.js'
import {useInvoiceStore} from "@/modules/AdminPanel/Invoice/InvoiceStore.js";
const loginStore = useLoginStore()

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
const invoiceData=reactive({
    customer_id:'',
    package_id:''
});

const dropdownPackages = computed(() =>
    loginStore?.packageList.map((item) => ({
        val: item.id,
        title: item.name + ' (' + item.type + ')',
    })));

const dropdownCustomers = computed(() =>
    loginStore?.customerList.map((item) => ({
        val: item.id,
        title: item.name,
    })));


const loaderStatusChange = (v) => {
    isShowLoader.value = v
}

const resetForm = () => {
    invoiceModal.value?.clearForm()
}

const submitForm = async() => {
    invoiceModal.value?.showLoader(true, appsbdUtls.translateGettext('Generating Invoice'));
    try {
        const response = await invoiceStore.genInvoice(invoiceData);
        msg.value = response.msg;
        if (response.status) {
           invoiceModal.value?.setMessageOnly(response.status)
            emit('reload')
            //emitClose()
        }
    }catch (e) {
         console.log(e);
    }
    invoiceModal.value?.showLoader(false)
}





const emitClose = () => {
    emit('close')
}
onMounted(() => {

});
</script>

<style scoped lang="scss">
/* Add your styles here if needed */
</style>
