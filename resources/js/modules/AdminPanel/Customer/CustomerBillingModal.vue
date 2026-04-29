<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-md"
        ref="detailModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >

        <template #header>
            <div class="d-flex align-items-center gap-2 w-100">
               <h6 class="mb-0" v-translate>client.info</h6>
            </div>
        </template>

        <!-- ================= Body ================= -->
        <template #body>
            <div class="row g-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3" v-translate>gbl.trx.info</h6>
                            <div class="row">
                                <div class="col-12 d-flex align-items-center justify-content-between py-2">
                                    <p class="mb-1 fw-medium" v-translate>gbl.ref.number</p>
                                    <p class="fw-semibold mb-1">{{ tempCustomer.ref_number }}</p>
                                </div>
                                <div class="col-12 d-flex align-items-center justify-content-between py-2">
                                    <p class="mb-1 fw-medium" v-translate>gbl.prev.bill</p>
                                    <p class="fw-semibold mb-1">{{ AppHelper.formatDateTime(tempCustomer.prev_bill_date) }}</p>
                                </div>

<!--                                    <p class="mb-1 fw-medium" v-translate>gbl.next.bill</p>
                                    <p class="fw-semibold mb-1">{{AppHelper.formatDateTime(tempCustomer.next_bill_date)  }}</p>-->
                                    <inline-edit-field
                                        @edit-start="isEditingAnyField = true"
                                        @edit-end="isEditingAnyField = false"
                                        :disabled="isLocked"
                                        v-model="tempCustomer.next_bill_date"
                                        label="gbl.next.bill"
                                        :edit_id="props.id"
                                        prop_key="next_bill_date"
                                        update_from="customer"
                                    >
                                        <template #editor="{ modelValue, updateModelValue }">
                                            <apbd-date-picker v-model="tempCustomer.next_bill_date"  name="next_bill_date" rules="required" visibility="focus" />
                                        </template>
                                    </inline-edit-field>

                                <div class="col-12 d-flex align-items-center justify-content-between py-2">
                                    <p class="mb-1 fw-medium me-2" v-translate>gbl.full.domain</p>
                                    <SubdomainInput
                                        @edit-start="isEditingAnyField = true"
                                        @edit-end="isEditingAnyField = false"
                                        v-model="tempCustomer.sub_domain"
                                        :mainDomain="rootData.domain"
                                        :disabled="isLocked"
                                        :customer-id="props.id"
                                        label="Sub Domain"
                                        placeholder="Enter Sub Domain"
                                    >
                                        <template #error>
                                            <ErrorMessage name="sub_domain" class="apbd-v-error"/>
                                        </template>
                                    </SubdomainInput>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>


        <template #footer>
            <div class="d-flex justify-content-end align-items-center w-100">


                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" @click="emitClose">
                        <translate>gbl.close</translate>
                    </button>


<!--                    <button type="button" v-if="tempCustomer.status !== 'A' && $CheckACL('np.customer-approve')" :disabled="isEditingAnyField"  class="btn btn-success px-4 d-flex align-items-center gap-2" @click="approveCustomer">-->
<!--                        <translate>gbl.approve</translate>-->
<!--                        <small-loader v-if="isApproveLoading"  :show="isApproveLoading" color="#ece" size="5" />-->
<!--                    </button>-->
                </div>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import {computed, onMounted, reactive, ref,inject,getCurrentInstance} from 'vue';
const { proxy } = getCurrentInstance();

const rootData = inject('rootData');
import {ApbdRadioButton, InputField,ApbdDatePicker,ResponseMsg,ApbdSwitchButton,AppLoader} from "@appsbd/vue3-appsbd-libs";
import Multiselect from "@vueform/multiselect";
import { Modal } from '@appsbd/vue3-appsbd-libs'
import appsbdUtls from "@/libs/AppsbdUtls.js";
import {useTempCustomerStore} from "@/modules/AdminPanel/TempCustomer/TempCustomerStore.js";
import {useCustomerStore} from "@/modules/AdminPanel/Customer/CustomerStore.js";
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import Rolling from "@/components/Rolling.vue";
import AppsbdUtls from "@/libs/AppsbdUtls.js";
import SmallLoader from "@/components/SmallLoader.vue";
import {ErrorMessage, Field} from "vee-validate";
import InlineEditField from "@/components/InlineEditField.vue";
import ContactNumberInput from "@/components/ContactNumberInput.vue";
import cities from "@/libs/cities.js";
import AppHelper from "../../../libs/AppHelper.js";
import SubdomainInput from "@/components/SubdomainInput.vue";


const props = defineProps({
    id: {
        default: null
    }
})
const emit = defineEmits(['close', 'reload'])
const tempStore=useTempCustomerStore();
const customerStore=useCustomerStore();
const detailModal = ref(null)
const msg = ref({})
const note = ref('');

const isShowLoader = ref(false)

const isApproveLoading = ref(false)

const isEditingAnyField = ref(false);



const loginStore = useLoginStore();



const cityList=cities;



const tempCustomer = reactive({
    name: '',
    email: '',
    contact_no: '',
    city: '',
    address: '',
    is_whatsapp: '',
    shop_name: '',
    shop_type: '',
    shop_address: '',
    pkg_price: 0.0,
    paid_amount: 0.0,
    pkg_id: 1,
    full_domain: '',
    sub_domain: '',
    ref_number: '',
    ref_no: '',
    ref_id: '',
    created_at: '',
    status: '',
    next_bill_date:'',
    prev_bill_date:'',
    amount_credit:'',
    amount_debit:'',
})

const oldData = reactive({})
const onPackageChange = (data) => {
    tempCustomer.pkg_price = data.price;
};
const options = ref([
    { val: "G", title: "register.gro", is_active: false },
    { val: "P", title: "register.pay", is_active: false },
    { val: "B", title: "register.bas", is_active: false },
    {val: "R", title: "register.res", is_active: false },
]);
const getOptions = computed( () =>{
    let pkgOptions = [];
    const selectedPkg = loginStore.packageList.find(p => p.id === tempCustomer.pkg_id);
        for (let opt of options.value)
        {
            if (!selectedPkg.shop_types?.includes(opt.val))
            {
                opt.disable = 'Y';
                opt.disable_tooltip = appsbdUtls.translateGettext('need.change');

            }
            pkgOptions.push(opt);
        }
    return pkgOptions;
});




const emitClose = () =>  {
    emit('close')
}

const loaderStatusChange = () => {}
const approveCustomer = async() => {

    isApproveLoading.value=true;
    let response = null;
    try {
         response = await customerStore.approvePayment({id: props.id});
         if(response.status)
         {
             emit('reload');
             emit('close');
         }

    }catch (e) {
       console.log(e);
    }
    AppsbdUtls.ShowServerResponseNotification(response.msg, 5000);
    isApproveLoading.value=false;
}

const getDetails = async() => {

    if (props.id != null) {
        detailModal.value?.showLoader(true, appsbdUtls.translateGettext('Loading Customer'));
        const response = await customerStore.getDetail({id: props.id})
        if (response.status) {
            Object.assign(tempCustomer, response.data)
            Object.assign(oldData, response.data)
            const selectedPkg = loginStore.packageList.find(p => p.id === tempCustomer.pkg_id);
            if (selectedPkg) {
                tempCustomer.pkg_price = selectedPkg.price;
            }

        }
        detailModal.value?.showLoader(false)
    }
}
const isLocked = computed(() => !proxy.$CheckACL('np.customer-update') && tempCustomer.status === 'C' );
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
