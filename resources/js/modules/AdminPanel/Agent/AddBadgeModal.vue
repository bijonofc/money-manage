<template>
    <Modal :modal-msg="msg"
           modal-size="modal-md"
           ref="badgeModal"
           @onSubmit="submitForm"
           @loading-status="loaderStatusChange"
           @close="emitClose">
        <template #header>
            <span> {{
                    props.badge_id ? AppsbdUtls.translateGettext('gbl.edit', {name: 'gbl.badge'}) : AppsbdUtls.translateGettext('gbl.add', {name: 'gbl.badge'})
                }}</span>
        </template>

        <template #body>
            <div class="row row-cols-1 row-cols-md-2">
                <div class="col">
                    <input-field label="gbl.badge.name" class="form-control form-control-sm" v-model="badge.title" name="name" rules="required"/>
                </div>
                <div class="col">
                    <input-field label="gbl.minimum.account" class="form-control form-control-sm text-center"
                                 v-model="badge.min_acc_open" name="minimum_account" type="number"
                                 rules="required|min_value:1"/>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-md-2">
                <div class="col">
                    <apbd-radio-button label="gbl.commission.type" name="com_type" class="form-check-input-sm"
                                       :options="com_option" :val="com_val" v-model="badge.com_type"/>
                </div>
                <div class="col">
                    <input-field label="gbl.commission.amount" class="form-control form-control-sm text-center"
                                 v-model="badge.com_amount" name="commission" type="number"
                                 rules="required|min_value:1">
                        <template #postfix>
                            {{badge.com_type == 'P' ? '%' : '৳'}}
                        </template>
                    </input-field>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-md-2">
                <div class="col">
                    <apbd-radio-button label="gbl.cus.dis.type" name="status" class="form-check-input-sm"
                                       :options="options" :val="val" v-model="badge.cus_dis_type"/>
                </div>
                <div class="col">
                    <input-field label="gbl.cus.dis.amount" class="form-control form-control-sm text-center"
                                 v-model="badge.cus_dis_amount" name="cus_dis_amount" type="number" rules="required|min_value:1">
                        <template #postfix>{{badge.cus_dis_type == 'P' ? '%' : '৳'}}</template>
                    </input-field>
                </div>
            </div>
            <div class="row row-cols-1">
                <div class="col">
                    <label for="desc" class="form-label" v-translate>gbl.description</label>
                    <Field label="Description" as="textarea" class="form-control form-control-sm" rows="5"
                           :placeholder="appsbdUtls.translateGettext('gbl.description')" id="desc" name="desc"
                           v-model="badge.description">

                    </Field>
                </div>
            </div>
        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
            <button v-if="props.badge_id && $CheckACL('np.agent-badge-update') " type="submit"
                    class="btn btn-sm btn-primary" v-translate>
                gbl.save.changes
            </button>
            <button v-if="!props.badge_id && $CheckACL('np.agent-badge-add')" type="submit"
                    class="btn btn-sm btn-primary" v-translate>
                gbl.save
            </button>
        </template>
    </Modal>
</template>

<script setup>
import {onMounted, reactive, ref} from "vue";
import {ApbdRadioButton, InputField, Modal} from "@appsbd/vue3-appsbd-libs";
import AppsbdUtls from "@/libs/AppsbdUtls.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";
import {Field} from "vee-validate";
import {useAgentStore} from "@/modules/AdminPanel/Agent/AgentStore.js";

const props = defineProps({
    badge_id: {
        default: null
    }
})

const emit = defineEmits(['close', 'reload'])
const msg = ref({})
const badgeModal = ref(null);
const isShowLoader = ref(false)

const val = 'P';
const options = [
    {val: "P", title: "gbl.percentage"},
    {val: "F", title: "gbl.fixed"},
]
const com_val = 'P';
const com_option = [
    {val: "P", title: "gbl.percentage"},
    {val: "F", title: "gbl.fixed"},
]
const oldData = reactive({})
const badge = reactive({
    title: '',
    min_acc_open: '',
    com_percentage: '',
    cus_dis_type: '',
    cus_dis_amount: '',
    description: ''
})
const agentStore = useAgentStore();

onMounted(() => {
    badgeDetails()
});

const badgeDetails = async () => {
    if (props.badge_id != null) {
        try {
            badgeModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.loading', {name: 'badge'}));
            const response = await agentStore.getBadge({id: props.badge_id});
            if (response.status) {
                Object.assign(badge, response.data)
                Object.assign(oldData, response.data)
            }
        } catch (e) {
            console.log(e);
        }
        badgeModal.value?.showLoader(false);
    }

}
const loaderStatusChange = (v) => {
    isShowLoader.value = v
}
const emitClose = () => {
    emit('close')
}
const submitForm = async () => {
    msg.value = {}
    if (props.badge_id != null) {
        try {
            const formData = getFormData()
            if (Object.keys(formData).length === 0) {
                msg.value = {error: ['No Change For Update']}
                badgeModal.value?.showMsgOnly(msg.value, false)
                return
            }
            badgeModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.updating', {name: 'badge'}))
            const response = await agentStore.updateBadge({id: props.badge_id, ...formData});
            msg.value = response.msg
            if (response.status) {
                badgeModal.value?.setMessageOnly(response.status);
                emit('reload')
            } else {
                badgeModal.value?.showMsgOnly(response.msg, false)
            }
            badgeModal.value?.showLoader(false)
        } catch (e) {
            console.log(e);
        }
        badgeModal.value?.showLoader(false)
    } else {
        try {
            badgeModal.value?.showLoader(true, AppsbdUtls.translateGettext('gbl.adding', {name: 'badge'}))
            const response = await agentStore.addBadge(badge);
            msg.value = response.msg;
            if (response.status) {
                badgeModal.value?.setMessageOnly(response.status)
                emit('reload')
            } else {
                badgeModal.value?.showMsgOnly(response.msg, false)
            }
        } catch (e) {
            console.log(e);
        }

        badgeModal.value?.showLoader(false)
    }

}

const getFormData = () => {
    if (props.badge_id != null) {
        const diff = {}
        for (const key in badge) {
            if (badge[key] !== oldData[key]) {
                diff[key] = badge[key]
            }
        }
        return diff
    }
    return {...badge}
}

</script>

<style scoped lang="scss">

</style>
