<template>
    <Modal :modal-msg="msg"
           modal-size="modal-lg"
           ref="agentModal"
           @onSubmit="submitForm"
           @loading-status="loaderStatusChange"
           @close="emitClose">
        <template #header>
            <span> {{
                    props.agent_id ? AppsbdUtls.translateGettext('gbl.edit', {name: 'gbl.agent'}) : AppsbdUtls.translateGettext('gbl.add', {name: 'gbl.agent'})
                }}</span>
        </template>
        <template #body>
            <div class="row row-cols-1 row-cols-sm-3 g-3">
                <div class="col">
                    <input-field label="gbl.name" class="form-control form-control-sm" v-model="agent.name" name="name"
                                 rules="required"/>
                </div>
                <div class="col">
                    <input-field label="gbl.email" class="form-control form-control-sm" v-model="agent.email"
                                 name="email" rules="required"/>
                </div>
                <div class="col">
                    <input-field label="gbl.username" :isDisable="props.agent_id != null ? true : false" class="form-control form-control-sm" v-model="agent.username"
                                 name="username" rules="required"/>
                </div>

            </div>
            <div class="row row-cols-1 row-cols-sm-3 g-3">
                <div class="col">
                    <contact-number-input class="form-control-sm p-0"   v-model="agent.contact_no" label="gbl.contact.no"/>
                </div>
                <div class="col">
                    <input-field label="gbl.negative.balance.limit" class="form-control form-control-sm text-center"
                                 v-model="agent.allowed_negative_balance" name="allowed_negative_balance" type="number" rules="min_value:0"/>
                </div>
                <div class="col">
                    <apbd-switch-button v-model="agent.is_whatsapp" id="is_whatsapp" container-class="form-switch-sm">
                        <template #topLabel>
                            <translate>is.whatsapp</translate>
                        </template>
                    </apbd-switch-button>
                </div>

            </div>
            <div class="row row-cols-1 row-cols-sm-3 g-3">
                <div class="col">
                    <apbd-dropdown label="gbl.badge" class="form-control form-control-sm" placeholder="Select Badge" :options="badge_options"
                                   v-model="agent.badge_id" name="badge"/>
                </div>
                <div class="col">
                    <input-field label="gbl.commission.value" class="form-control form-control-sm text-center"
                                 v-model="agent.badge_commission_value" name="badge_value" type="number" rules="required|min_value:1"/>
                </div>
                <div class="col">
                    <apbd-radio-button label="gbl.commission.type" name="com_type" class="form-check-input-sm"
                                       :options="options" :val="com_val" v-model="agent.com_type"/>
                </div>
            </div>
            <location-selector :model-value="agent" responsive-column="row-cols-1 row-cols-md-3 g-3"/>

        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
            <button v-if="props.agent_id && $CheckACL('np.agent-update')" type="submit" class="btn btn-sm btn-primary" v-translate>
                gbl.save.changes
            </button>
            <button v-else type="submit" class="btn btn-sm btn-primary" v-translate>
                gbl.save
            </button>
        </template>

    </Modal>
</template>

<script setup>
import {
    Modal,
    InputField,
    ApbdDropdown, ApbdRadioButton, ApbdSwitchButton
} from "@appsbd/vue3-appsbd-libs";
import {onMounted, reactive, ref, watch,computed} from "vue";
import {Field, ErrorMessage} from 'vee-validate'
import AppsbdUtls from "@/libs/AppsbdUtls.js";
import ContactNumberInput from "@/components/ContactNumberInput.vue";
import {useAgentStore} from "@/modules/AdminPanel/Agent/AgentStore.js";
import LocationSelector from "@/components/LocationSelector.vue";

const props = defineProps({
    agent_id: {
        default: null
    },
    badge_list: {
        default: []
    }
})

const emit = defineEmits(['close', 'reload'])

const agentModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)

const agent = reactive({
    name: '',
    email: '',
    username: '',
    contact_no: '',
    badge_id: '',
    badge_commission_value: '',
    is_whatsapp: '',
    referral_code: '',
    allowed_negative_balance: '',
    status:'A',
    country:'',
    state:'',
    zip_code:'',
    city:'',
    address:'',
})
const oldData = reactive({})

const options = [
    {val: "P", title: "gbl.percentage"},
    {val: "F", title: "gbl.fixed"},
]
const com_val = 'P';
const agentStore = useAgentStore();
const badge_options = ref([]);
const discount_type = 'P';
watch(() => props.badge_list, (newVal) => {
    if (newVal?.length) {
        badge_options.value = newVal.map(b => ({val: b.id, title: b.title}))
    }
}, {immediate: true});

watch(
    () => agent.badge_id,
    (newBadgeId) => {
        if (!newBadgeId) return;

        const selectedBadge = props.badge_list.find(
            b => b.id === newBadgeId
        );

        if (selectedBadge) {
            agent.badge_commission_value = selectedBadge.com_amount;
            agent.com_type = selectedBadge.com_type;
        }
    }
);

onMounted(() => {
    agentDetails();
})

const agentDetails = async () => {
    if (props.agent_id != null) {
        try {
            agentModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.loading', {name: 'agent'}));
            const response = await agentStore.getAgent({id: props.agent_id});
            if (response.status) {
                Object.assign(agent, response.data);
                Object.assign(oldData, response.data);
            }
        } catch (e) {
            console.log(e);
        }
        agentModal.value?.showLoader(false);
    }
}
const loaderStatusChange = (v) => {
    isShowLoader.value = v
}
const submitForm = async () => {
    if (props.agent_id != null) {
        try {
            const formData = getFormData()
            if (Object.keys(formData).length === 0) {
                msg.value = {error: ['No Change For Update']}
                agentModal.value?.showMsgOnly(msg.value, false)
                return
            }
            agentModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.update', {name: 'agent'}));
            const response = await agentStore.updateAgent({id: props.agent_id, ...formData})
            msg.value = response.msg
            if (response.status) {
                agentModal.value?.setMessageOnly(response.status);
                emit('reload')
            } else {
                agentModal.value?.showMsgOnly(response.msg, false)
            }
            agentModal.value?.showLoader(false)
        } catch (e) {
            console.log(e);
        }
        agentModal.value?.showLoader(false);
    } else {
        try {
            agentModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.adding', {name: 'agent'}));
            const response = await agentStore.addAgent(agent);
            msg.value = response.msg;
            if (response.status) {
                agentModal.value?.setMessageOnly(response.status)
                emit('reload')
            } else {
                agentModal.value?.showMsgOnly(response.msg, false)
            }
        } catch (e) {
            console.log(e);
        }
        agentModal.value?.showLoader(false);
    }
}
const emitClose = () => {
    emit('close')
}
const getFormData = () => {
    if (props.agent_id != null) {
        const diff = {}
        for (const key in agent) {
            if (agent[key] !== oldData[key]) {
                diff[key] = agent[key]
            }
        }
        return diff
    }
    return {...agent}
}

</script>

<style scoped lang="scss">

</style>
