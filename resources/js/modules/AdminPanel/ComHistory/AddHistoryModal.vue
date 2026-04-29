<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-lg"
        ref="historyModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >

        <template #header>
            <span>
                <translate>gbl.add.com</translate>
            </span>
        </template>


        <template #body>

            <div class="row g-3">

                <div class="col-md-6 mb-0">
                    <label class="form-label" v-translate>gbl.customer.name</label>
                    <Field name="customer_id" rules="required" v-model="form.customer_id" label="gbl.customer.name">
                        <multiselect
                            v-model="form.customer_id"
                            label="name"
                            valueProp="id"
                            :options="loginStore.customerList"
                            :clearOnSelect="true"
                            :close-on-select="true"
                            :searchable="true"
                        />
                    </Field>
                    <ErrorMessage name="customer_id" class="apbd-v-error"/>
                </div>


                <div class="col-md-6 mb-0">
                    <apbd-dropdown
                        label="gbl.com.type"
                        name="type"
                        v-model="form.type"
                        :options="dropdownTypes"
                        rules="required"
                        placeholder="Select"
                    />
                </div>

                <!-- SUBJECT -->
                <div class="col-md-6 m-0">
                    <input-field label="gbl.com.subject" v-model="form.subject" name="subject" rules="required"/>
                </div>
                <div class="col-md-6 m-0">
                    <apbd-date-picker v-model="form.next_follow_up" label="gbl.next.follow.up" name="next_follow_up" rules="required" visibility="focus" />
                </div>

                <!-- NOTE -->
                <div class="col-md-12 m-0">
                    <label class="form-label" v-translate>gbl.note</label>
                    <Field as="textarea" rows="2" class="form-control form-control-sm" v-model="form.note" name="note"/>
                </div>
            </div>

            <div class="card mt-3 p-3 shadow-md">

                <template v-if="form.type === 'C'">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input-field label="gbl.duration" v-model="content.duration" name="duration" rules="required"/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" v-translate>gbl.call.summary</label>
                            <Field as="textarea" rows="2" class="form-control form-control-sm" label="gbl.call.summary" v-model="content.call_summary" name="call_summary"/>
                        </div>
                    </div>
                </template>

                <!-- EMAIL -->
                <template v-if="form.type === 'E'">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input-field label="gbl.subject" v-model="content.subject" name="content_subject" rules="required"/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" v-translate>gbl.email.body</label>
                            <Field as="textarea" rows="2" class="form-control form-control-sm" label="gbl.email.body" v-model="content.email_body" name="email_body"/>
                        </div>
                    </div>
                </template>

                <!-- SMS -->
                <template v-if="form.type === 'S'">
                    <label class="form-label" v-translate>gbl.sms.text</label>
                    <Field as="textarea" rules="required" label="gbl.sms.text" rows="2" class="form-control form-control-sm" v-model="content.sms_text" name="sms_text"/>
                    <ErrorMessage name="sms_text" class="apbd-v-error"/>
                </template>

                <!-- MEETING -->
                <template v-if="form.type === 'M'">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input-field label="gbl.activity" v-model="content.activity" name="activity" rules="required"/>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" v-translate>gbl.meeting.note</label>
                            <Field as="textarea" rows="2" class="form-control form-control-sm" v-model="content.meeting_notes" name="meeting_notes"/>
                        </div>
                    </div>
                </template>

                <!-- OTHER JSON -->
                <template v-if="form.type === 'O'">
                    <label class="form-label" v-translate>gbl.other</label>
                    <Field as="textarea" label="gbl.other" rules="required" rows="2" class="form-control form-control-sm" v-model="content.other" name="other"/>
                    <ErrorMessage name="other" class="apbd-v-error"/>
                </template>

            </div>

        </template>

        <!-- FOOTER -->
        <template #footer>
            <button class="btn btn-primary btn-sm" type="submit" v-translate>gbl.save</button>
            <button class="btn btn-danger btn-sm" @click="emitClose" v-translate>gbl.close</button>
        </template>
    </Modal>
</template>



<script setup>
import { ErrorMessage, Field } from 'vee-validate'
import Multiselect from "@vueform/multiselect";
import { InputField, ApbdDropdown,ApbdDatePicker } from '@appsbd/vue3-appsbd-libs'
import { ref, reactive, computed } from 'vue'
import { Modal } from '@appsbd/vue3-appsbd-libs'
import { useComHistoryStore } from "./ComHistoryStore"
import { useLoginStore } from "@/modules/AdminPanel/User/loginStore.js"
import appsbdUtls from "@/libs/AppsbdUtls.js";

const emit = defineEmits(['close','reload'])

const comHistoryStore = useComHistoryStore()
const loginStore = useLoginStore()
const historyModal = ref(null)

const form = reactive({
    customer_id: '',
    type: 'C',
    content: {},
    note: '',
    subject: '',
    status: 'S',
    next_follow_up: null,
})

const content = reactive({})
const msg = ref({})

const dropdownTypes = computed(() => [
    { val: 'C', title: 'Call' },
    { val: 'E', title: 'Email' },
    { val: 'M', title: 'Meeting' },
    { val: 'S', title: 'SMS' },
    { val: 'O', title: 'Other' },
])

const submitForm = async () => {
    historyModal.value?.showLoader(true, appsbdUtls.translateGettext('Adding History'));
    try {
        form.content = content;
        const response = await comHistoryStore.addHistory(form);
        msg.value = response.msg;
        if(response.status){
            historyModal.value?.setMessageOnly(response.status);
            emit('reload');

        }else{
            historyModal.value?.showMsgOnly(response.msg, false);
        }
    }catch (e) {
        console.log(e);
    }

    historyModal.value?.showLoader(false);
}

const loaderStatusChange = () => {}
const emitClose = () => emit('close')
</script>



<style scoped>

</style>

