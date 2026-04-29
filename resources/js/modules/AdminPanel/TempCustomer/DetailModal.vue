<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-xl"
        ref="detailModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >

        <template #header>
            <div class="d-flex align-items-center gap-2 w-100">
               <h6 class="mb-0" v-translate>customer.info</h6>
            </div>
        </template>

        <!-- ================= Body ================= -->
        <template #body>
            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3" v-translate>gbl.customer.details</h6>

                                <inline-edit-field
                                    @edit-start="isEditingAnyField = true"
                                    @edit-end="isEditingAnyField = false"
                                    :disabled="isLocked"
                                    v-model="tempCustomer.name"
                                    label="gbl.name"
                                    type="text"
                                    :edit_id="props.id"
                                    prop_key="name"
                                />
                                <inline-edit-field
                                    @edit-start="isEditingAnyField = true"
                                    @edit-end="isEditingAnyField = false"
                                    :disabled="isLocked"
                                    v-model="tempCustomer.email"
                                    label="gbl.email"
                                    type="email"
                                    :edit_id="props.id"
                                    prop_key="email"
                                />
                                <inline-edit-field
                                    @edit-start="isEditingAnyField = true"
                                    @edit-end="isEditingAnyField = false"
                                    :disabled="isLocked"
                                    v-model="tempCustomer.contact_no"
                                    label="gbl.contact.no"
                                    :edit_id="props.id"
                                    prop_key="contact_no"
                                >
                                    <template #editor="{ modelValue, 'update:modelValue': updateModelValue }">
                                        <ContactNumberInput :modelValue="modelValue" class="form-control-sm" @update:modelValue="updateModelValue"/>
                                    </template>
                                </inline-edit-field>
                                <inline-edit-field
                                    @edit-start="isEditingAnyField = true"
                                    @edit-end="isEditingAnyField = false"
                                    :disabled="isLocked"
                                    v-model="tempCustomer.city"
                                    label="gbl.city"
                                    :edit_id="props.id"
                                    prop_key="city"
                                >
                                    <template #editor="{ modelValue, updateModelValue }">
                                        <multiselect
                                            class="multiselect-sm"
                                            :modelValue="modelValue"
                                            :options="cityList"
                                            label="title"
                                            valueProp="id"
                                            :searchable="true"
                                            @update:modelValue="updateModelValue"
                                        />
                                    </template>
                                </inline-edit-field>
                                <inline-edit-field
                                    @edit-start="isEditingAnyField = true"
                                    @edit-end="isEditingAnyField = false"
                                    :disabled="isLocked"
                                    v-model="tempCustomer.address"
                                    label="gbl.address"
                                    type="textarea"
                                    :edit_id="props.id"
                                    prop_key="address"
                                />

                                <inline-edit-field
                                    @edit-start="isEditingAnyField = true"
                                    @edit-end="isEditingAnyField = false"
                                    :disabled="isLocked"
                                    v-model="tempCustomer.is_whatsapp"
                                    label="is.whatsapp"
                                    :edit_id="props.id"
                                    prop_key="is_whatsapp"
                                >
                                    <template #editor="{ modelValue, updateModelValue }">
                                        <apbd-switch-button
                                            :modelValue="modelValue"
                                            @update:modelValue="updateModelValue"
                                            id="is_whatsapp"
                                            :true-value="'Y'"
                                            :false-value="'N'"
                                            container-class="form-switch-md"
                                        />
                                    </template>
                                </inline-edit-field>


                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3" v-translate>gbl.shop.package.details</h6>

                                <inline-edit-field
                                    @edit-start="isEditingAnyField = true"
                                    @edit-end="isEditingAnyField = false"
                                    :disabled="isLocked"
                                    v-model="tempCustomer.shop_name"
                                    label="gbl.shop.name"
                                    type="text"
                                    :edit_id="props.id"
                                    prop_key="shop_name"
                                />
                                <inline-edit-field
                                    @edit-start="isEditingAnyField = true"
                                    @edit-end="isEditingAnyField = false"
                                    :disabled="isLocked"
                                    v-model="tempCustomer.shop_type"
                                    label="gbl.shop.type"
                                    :edit_id="props.id"
                                    prop_key="shop_type"
                                >
                                    <template #editor="{ modelValue, updateModelValue }">
                                        <apbd-radio-button   name="gbl.shop.type" class="form-check-input-sm" rules="required" :options="getOptions"   :modelValue="modelValue" @update:modelValue="updateModelValue"/>
                                    </template>
                                </inline-edit-field>
                                <inline-edit-field
                                    @edit-start="isEditingAnyField = true"
                                    @edit-end="isEditingAnyField = false"
                                    :disabled="isLocked"
                                    v-model="tempCustomer.shop_address"
                                    label="gbl.shop.address"
                                    type="textarea"
                                    :edit_id="props.id"
                                    prop_key="shop_address"
                                />
                                <ul class="list-group list-group-flush">
                                    <inline-edit-field
                                        @edit-start="isEditingAnyField = true"
                                        @edit-end="isEditingAnyField = false"
                                        :disabled="isLocked"
                                        v-model="tempCustomer.pkg_id"
                                        label="gbl.package"
                                        :edit_id="props.id"
                                        prop_key="pkg_id"
                                        @update-pkg-info="onPackageChange"
                                    >
                                        <template #editor="{ modelValue, updateModelValue }">
                                            <multiselect
                                                class="multiselect-sm"
                                                :modelValue="modelValue"
                                                :options="loginStore.packageList"
                                                label="name"
                                                valueProp="id"
                                                searchable
                                                @update:modelValue="updateModelValue"
                                                @update-pkg-info="onPackageChange"
                                            >
                                                <template #option="{ option }">
                                                    <div class="d-flex justify-content-between w-100">
                                                        <span>{{ option.name }}</span>
                                                        <span>{{ option.type === 'Y' ? 'Yearly' : 'Monthly' }}</span>
                                                        <span>৳ {{ parseFloat(option.price).toFixed(2) }}</span>
                                                    </div>
                                                </template>

                                                <template #singleLabel="{ option }">
                                                    <div class="d-flex justify-content-between w-100">
                                                        <span>{{ option.name }}</span>
                                                        <span>{{ option.type === 'Y' ? 'Yearly' : 'Monthly' }}</span>
                                                        <span>৳ {{ parseFloat(option.price).toFixed(2) }}</span>
                                                    </div>
                                                </template>
                                            </multiselect>
                                        </template>
                                    </inline-edit-field>

                                <li class="list-group-item d-flex justify-content-between py-2">
                                    <span class="fw-medium"><translate>gbl.pkg.price</translate>:</span>
                                    <strong>
                                        {{appHelper.toLocalizedDigits(tempCustomer.pkg_price,'bn')}}
                                        <translate>gbl.tk</translate>
                                    </strong>
                                </li>
<!--                                    <inline-edit-field-->
<!--                                        @edit-start="isEditingAnyField = true"-->
<!--                                        @edit-end="isEditingAnyField = false"-->
<!--                                        :disabled="isLocked"-->
<!--                                        v-model="tempCustomer.paid_amount"-->
<!--                                        label="gbl.given.amount"-->
<!--                                        type="number"-->
<!--                                        :edit_id="props.id"-->
<!--                                        prop_key="paid_amount"-->
<!--                                    />-->


                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Transaction Section -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="fw-bold text-primary mb-0" v-translate>gbl.trx.info</h6>
                                <small class="text-warning mb-0" v-translate>gbl.pyt.nrc</small>
                            </div>
                            <div class="row">
<!--                                <div class="col-md-6">-->
<!--                                    <inline-edit-field-->
<!--                                        @edit-start="isEditingAnyField = true"-->
<!--                                        @edit-end="isEditingAnyField = false"-->
<!--                                        :disabled="isLocked"-->
<!--                                        v-model="tempCustomer.ref_no"-->
<!--                                        label="gbl.bkash.number"-->
<!--                                        :edit_id="props.id"-->
<!--                                        prop_key="ref_no"-->
<!--                                    >-->
<!--                                        <template #editor="{ modelValue, 'update:modelValue': updateModelValue }">-->
<!--                                            <ContactNumberInput class="form-control-sm" :modelValue="modelValue" @update:modelValue="updateModelValue"/>-->
<!--                                        </template>-->
<!--                                    </inline-edit-field>-->

<!--                                </div>-->
<!--                                &lt;!&ndash; Transaction ID (Editable) &ndash;&gt;-->
<!--                                <div class="col-md-6">-->
<!--                                    <inline-edit-field-->
<!--                                        @edit-start="isEditingAnyField = true"-->
<!--                                        @edit-end="isEditingAnyField = false"-->
<!--                                        :disabled="isLocked"-->
<!--                                        v-model="tempCustomer.ref_id"-->
<!--                                        label="gbl.trx.id"-->
<!--                                        type="text"-->
<!--                                        :edit_id="props.id"-->
<!--                                        prop_key="ref_id"-->
<!--                                    />-->
<!--                                </div>-->
                                <div class="col-md-6 d-flex align-items-center justify-content-between py-2">
                                    <p class="mb-1 fw-medium" v-translate>gbl.ref.number</p>
                                    <p class="fw-semibold mb-1">{{ tempCustomer.ref_number }}</p>
                                </div>
                                <div class="col-md-6 d-flex align-items-center justify-content-between py-2">
                                    <p class="mb-1 fw-medium" v-translate>gbl.created</p>
                                    <p class="fw-semibold mb-1">{{ AppHelper.formatDateTime(tempCustomer.created_at) }}</p>
<!--                                    <p class="fw-semibold mb-1">{{ AppHelper.formatDate(tempCustomer.created_at) }}</p>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3" v-translate>gbl.admin.note</h6>
                            <Field v-model="note" label="note" name="note" @input="onInput"  as="textarea" class="form-control form-control-sm" rows="2" :placeholder="appsbdUtls.translateGettext('write.note')">

                            </Field>
                            <span v-if="showError" class="apbd-v-error" v-translate>gbl.note.required</span>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ================= Footer ================= -->
        <template #footer>
            <div class="d-flex justify-content-end align-items-center w-100">


                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" @click="emitClose">
                        <translate>gbl.close</translate>
                    </button>
<!--                    <button v-if="tempCustomer.status !== 'C' && tempCustomer.status !== 'R' && $CheckACL('np.customer-approve')"  :disabled="isEditingAnyField" type="button" class="btn btn-danger px-4" @click="rejectCustomer">-->
<!--                       <translate>gbl.reject</translate>-->
<!--                        <small-loader v-if="isRejectLoading"  :show="isRejectLoading" color="#ece" size="5" />-->
<!--                    </button>-->

<!--                    <button type="button"  :disabled="isEditingAnyField" v-if="tempCustomer.status !='C' && tempCustomer.status =='P'  && $CheckACL('np.customer-approve')" class="btn btn-success px-4 d-flex align-items-center gap-2" @click="approveCustomer">-->
<!--                        <translate>gbl.approve</translate>-->
<!--                        <small-loader v-if="isApproveLoading"  :show="isApproveLoading" color="#ece" size="5" />-->
<!--                    </button>-->
                </div>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import {computed, onMounted, reactive, ref,getCurrentInstance} from 'vue';
const { proxy } = getCurrentInstance();
import {ApbdRadioButton, InputField,ResponseMsg,ApbdSwitchButton,AppLoader} from "@appsbd/vue3-appsbd-libs";
import Multiselect from "@vueform/multiselect";
import { Modal } from '@appsbd/vue3-appsbd-libs'
import appsbdUtls from "@/libs/AppsbdUtls.js";
import {useTempCustomerStore} from "@/modules/AdminPanel/TempCustomer/TempCustomerStore.js";
import AppsbdUtls from "@/libs/AppsbdUtls.js";
import SmallLoader from "@/components/SmallLoader.vue";
import {ErrorMessage, Field} from "vee-validate";
import InlineEditField from "@/components/InlineEditField.vue";
import ContactNumberInput from "@/components/ContactNumberInput.vue";
import cities from "@/libs/cities.js";
import AppHelper from "../../../libs/AppHelper.js";


const props = defineProps({
    id: {
        default: null
    }
})
const emit = defineEmits(['close', 'reload'])
const tempStore=useTempCustomerStore();
const detailModal = ref(null)
const msg = ref({})
const note = ref('');

const isShowLoader = ref(false)
const showError = ref(false)
const isApproveLoading = ref(false)
const isRejectLoading = ref(false)
const isEditingAnyField = ref(false);
import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import appHelper from "@/libs/AppHelper.js";

const loginStore = useLoginStore();



const cityList=cities;


const paidAmountDisplay = computed(() => {
    return AppHelper.toLocalizedDigits(tempCustomer.paid_amount,'bn');
});
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
    note:'',
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


function getType(type) {
    return type === "Y" ? "Yearly" : "Monthly";
}





const emitClose = () =>  {
    emit('close')
}

const loaderStatusChange = () => {}
const approveCustomer = async() => {
    if (!note.value || note.value.trim() === '') {
        showError.value = true;
        return;
    }
    isApproveLoading.value=true;
    let response = null;
    try {
         response = await tempStore.approvePayment({id: props.id,note:note.value});
         if(response.status)
         {
             tempCustomer.status=response.data.status;
             emit('reload');
             emit('close');
         }

    }catch (e) {
       console.log(e);
    }
    AppsbdUtls.ShowServerResponseNotification(response.msg, 5000);
    isApproveLoading.value=false;
}

const rejectCustomer = async() => {

    if (!note.value || note.value.trim() === '') {
        showError.value = true;
        return;
    }
    let response = null;
    isRejectLoading.value=true;
    try {
        response = await tempStore.rejectPayment({id: props.id,note:note.value});
        if(response.status)
        {
            tempCustomer.status=response.data.status;
            emit('reload');
            emit('close');
        }

    }catch{
        console.log(e);
    }
    AppsbdUtls.ShowServerResponseNotification(response.msg, 5000);
  isRejectLoading.value=false;
}
const onInput=()=>{

    if (note.value!=''){
        showError.value=false;
    }
}
const getDetails = async() => {

    if (props.id != null) {
        detailModal.value?.showLoader(true, appsbdUtls.translateGettext('Loading Customer'));
        const response = await tempStore.getDetail({id: props.id})
        if (response.status) {
            Object.assign(tempCustomer, response.data)
            Object.assign(oldData, response.data)
            const selectedPkg = loginStore.packageList.find(p => p.id === tempCustomer.pkg_id);
            if (selectedPkg) {
                tempCustomer.pkg_price = selectedPkg.price;
            }
            if(response.data.note)
            {
                note.value=response.data.note;
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
</style>
