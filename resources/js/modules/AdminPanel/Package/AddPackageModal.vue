<template>
    <Modal
        :modal-msg="msg"
        modal-size="modal-lg"
        ref="packageModal"
        @onSubmit="submitForm"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <template #header>
            <div>
           <span v-if="props.package_id">
                {{ AppsbdUtls.translateGettext('gbl.edit', {name: 'package'}) }}
           </span>
                <span v-else>
               {{ AppsbdUtls.translateGettext('gbl.add', {name: 'package'}) }}
          </span>
            </div>
        </template>

        <template #body>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <input-field label="gbl.name" class="form-control form-control-sm" v-model="packages.name"
                                 name="name" rules="required"/>
                </div>
                <div class="col-12 col-md-6">
                    <input-field type="number" label="gbl.price" class="form-control form-control-sm text-end"
                                 v-model="packages.price" name="price" rules="required|numeric|min_value:1"/>
                </div>
                <div class="col-12 col-md-8">
                    <apbd-check-box id="id" :options="types" v-model="packages.shop_types" :val="val1"
                                    class="form-check-input-sm" name="shop_types" label="gbl.shop.type" :limit="4"
                                    rules="required"/>
                </div>

                <div class="col-12 col-md-4">
                    <apbd-radio-button label="gbl.type" name="type" class="form-check-input-sm" :options="options"
                                       :val="val" v-model="packages.type"/>
                </div>
                <div class="col-12">
                    <label for="short_desc" class="form-label" v-translate>gbl.short.desc</label>
                    <Field label="short.desc" as="textarea" class="form-control form-control-sm" rows="2"
                           id="description" name="short_desc" v-model="packages.short_desc">

                    </Field>
                </div>
                <div class="col-12 col-md-6">
                    <apbd-switch-button v-model="packages.is_extended" id="is_extended" container-class="form-switch-md">
                        <template #topLabel>
                            <translate>
                                gbl.is.ext
                            </translate>
                        </template>
                    </apbd-switch-button>
                </div>
                <div class="col-12 col-md-6">
                    <input-field  v-if="packages.is_extended=='Y'" type="number" label="gbl.ext.month" class="form-control form-control-sm text-end"
                                 v-model="packages.extended_month" name="extended_month"
                                 rules="required|min_value:1|max_value:12|numeric"/>
                </div>


            </div>
        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger" @click="emitClose" v-translate>
                gbl.close
            </button>
            <button v-if="$CheckACL('np.package-update') && props.package_id" type="submit"
                    class="btn btn-sm btn-primary" v-translate>
                gbl.save.changes
            </button>
            <button v-if="$CheckACL('np.package-add') && !props.package_id" type="submit" class="btn btn-sm btn-primary"
                    v-translate>
                gbl.save
            </button>
        </template>
    </Modal>
</template>

<script setup>
import {ref, reactive, watch, onMounted} from 'vue'

import {
    ApbdFilterPanel,
    ApbdRadioButton,
    Modal,
    ResponseMsg,
    InputField,
    ApbdCheckBox,
    ApbdSwitchButton
} from '@appsbd/vue3-appsbd-libs'
import {Field, ErrorMessage, Form} from 'vee-validate'

import appsbdUtls from '@/libs/AppsbdUtls.js'
import Multiselect from '@vueform/multiselect'
import AppsbdUtls from '@/libs/AppsbdUtls.js'
import {usePackageStore} from "@/modules/AdminPanel/Package/PackageStore.js";

const props = defineProps({
    package_id: {
        default: null
    }
})

const emit = defineEmits(['close', 'reload'])

const packageStore = usePackageStore()


const packageModal = ref(null)
const msg = ref({})
const isShowLoader = ref(false)

const packages = reactive({
    name: '',
    short_desc: '',
    is_extended: 'N',
    type: 'M',
    extended_month: '',
    shop_types: [],
    price: 0,
    regular_price: 0
})
const oldData = reactive({})
const options = [
    {val: "M", title: "Monthly"},
    {val: "Y", title: "Yearly"},
];
const types = [
    {val: "G", title: "register.gro"},
    {val: "R", title: "register.res"},
    {val: "P", title: "register.pay"},
    {val: "B", title: "register.bas"},
];
const val = ""
const val1 = []

const loaderStatusChange = (v) => {
    isShowLoader.value = v
}

const resetForm = () => {
    packageModal.value?.clearForm()
}

// Load if editing
const loadPackage = async () => {
    if (props.package_id != null) {
        packageModal.value?.showLoader(true, appsbdUtls.translateGettext('Loading Package'));
        const response = await packageStore.getPackage({
            package_id: props.package_id
        })

        if (response.status) {
            Object.assign(packages, response.data)
            Object.assign(oldData, response.data)
        }
        packageModal.value?.showLoader(false)
    }
}

const getFormData = () => {
    if (props.package_id != null) {
        const diff = {}
        for (const key in packages) {
            if (packages[key] !== oldData[key]) diff[key] = packages[key]
        }
        return diff
    }
    return {...packages}
}

const submitForm = async () => {
    msg.value = {}
    if (packages.is_extended == 'N') {
        packages.extended_month = 0;
    }
    if (props.package_id != null) {
        const formData = getFormData()
        if (Object.keys(formData).length === 0) {
            msg.value = {error: ['No Change For Update']}
            packageModal.value?.showMsgOnly(msg.value, false)
            return
        }
        packageModal.value?.showLoader(true, appsbdUtls.translateGettext('Updating Project'))
        const response = await packageStore.updatePackage({id: props.package_id, ...formData})

        msg.value = response.msg
        if (response.status) {
            packageModal.value?.setMessageOnly(response.status);
            emit('reload')
        } else {
            packageModal.value?.showMsgOnly(response.msg, false)
        }
        packageModal.value?.showLoader(false)

    } else {
        packageModal.value?.showLoader(true, appsbdUtls.translateGettext('Adding Package'))
        const response = await packageStore.addPackage(packages)
        msg.value = response.msg
        if (response.status) {
            packageModal.value?.setMessageOnly(response.status)
            emit('reload')
            //resetForm()
        } else {
            packageModal.value?.showMsgOnly(response.msg, false)
        }
        packageModal.value?.showLoader(false)
    }
}

const emitClose = () => {
    emit('close')
}
onMounted(() => loadPackage());
</script>

<style scoped lang="scss">
/* Add your styles here if needed */
</style>
