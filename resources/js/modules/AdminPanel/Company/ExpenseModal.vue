<template>
    <Modal :modal-msg="msg" modal-size="modal-md" ref="expenseModal" @onSubmit="submitForm" @loading-status="loaderStatusChange" @close="emitClose">

        <template #header>
            <div class="d-flex align-items-center gap-2 w-100">
                <h6 class="mb-0" v-translate>
                    {{props.type==='expense'? 'gbl.add.expense':'gbl.add.income'}}
                </h6>
            </div>
        </template>
        <template #body>
            <div class="row row-cols-1 g-3">
                <income-expense-form
                    v-model:expense="expense"
                    :uploaded-images="uploadedImages"
                    @remove="removeImage"
                />
            </div>

        </template>


        <template #footer>
            <div class="d-flex justify-content-end align-items-center w-100">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" @click="emitClose">
                        <translate>gbl.close</translate>
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary" v-translate>
                        gbl.save
                    </button>

                </div>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive } from 'vue'

import { Modal } from '@appsbd/vue3-appsbd-libs'

import {useCompanyStore} from "@/modules/AdminPanel/Company/CompanyStore.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";
import IncomeExpenseForm from "@/modules/AdminPanel/Company/IncomeExpenseForm.vue";
const companyStore=useCompanyStore();
const uploadedImages = ref([])


const emit = defineEmits(['add'])
const props=defineProps({
    type:{default:'expense',required:true}
})
const expense = reactive({
    amount: '',
    files: [],
    note: ''
})
const showMsgOnly = ref(false)
const expenseModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)

const emitClose = () =>  {
    emit('close')
}

const loaderStatusChange = () => {}
async function submitForm() {
    try {
        expenseModal.value?.showLoader(true, appsbdUtls.translateGettext(props.type === 'expense' ? 'gbl.add.expense' : 'gbl.add.income'));

        let response;

        if (props.type === 'expense') {
            response = await companyStore.addExpense(expense);
        } else {
            response = await companyStore.addIncome(expense);
        }

        msg.value = response.msg;

        if (response.status) {
            expenseModal.value?.setMessageOnly(true);
            emit('reload');
        } else {
            expenseModal.value?.showMsgOnly(response.msg, false);
        }

    } catch (e) {
        console.error('Submit failed', e);
    }
        expenseModal.value?.showLoader(false);

}


</script>

<style scoped>

</style>
<style scoped lang="scss">

</style>
