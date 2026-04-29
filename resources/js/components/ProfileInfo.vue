<template>
    <div class="profile-info">

        <!-- NAME -->
        <response-msg :message="msgs"/>

        <div class="row align-items-center mb-2">
            <div class="col-md-4 fw-semibold" v-translate>gbl.name</div>
            <div class="col-md-8 text-muted">
                <input-field v-if="isEdit"  v-model="user.name" container-class="mb-0" name="name" />
                <span v-else>{{ props.user.name || '-' }}</span>
            </div>
        </div>

        <!-- USERNAME -->
        <div class="row align-items-center mb-2">
            <div class="col-md-4 fw-semibold" v-translate>gbl.username</div>
            <div class="col-md-8 text-muted">
                <input-field v-if="isEdit" v-model="user.username" name="username" />
                <span v-else>{{ props.user.username || '-' }}</span>
            </div>
        </div>

        <!-- EMAIL -->
        <div class="row align-items-center mb-2">
            <div class="col-md-4 fw-semibold" v-translate>gbl.email</div>
            <div class="col-md-8 text-muted">
                <input-field v-if="isEdit" v-model="user.email" name="email" rules="email" />
                <span v-else>{{ props.user.email || '-' }}</span>
            </div>
        </div>

        <!-- CONTACT -->
        <div class="row align-items-center mb-2">
            <div class="col-md-4 fw-semibold" v-translate>gbl.contact</div>
            <div class="col-md-8 text-muted">
                <contact-number-input v-if="isEdit" name="contact_no" v-model="user.contact_no" />
                <span v-else>{{ props.user.contact_no || '-' }}</span>
            </div>
        </div>

        <!-- WHATSAPP -->
        <div class="row align-items-center mb-2">
            <div class="col-md-4 fw-semibold" v-translate>is.whatsapp</div>
            <div class="col-md-8 text-muted">
                <apbd-switch-button
                    v-if="isEdit"
                    name="is_whatsapp"
                    v-model="user.is_whatsapp"
                    container-class="form-switch-sm"
                />
                <span v-else>{{ props.user.is_whatsapp ? 'Yes' : 'No' }}</span>
            </div>
        </div>

        <!-- COUNTRY -->
        <div class="row align-items-center mb-2">
            <div class="col-md-4 fw-semibold" v-translate>gbl.country</div>
            <div class="col-md-8 text-muted">
                <multiselect
                    v-if="isEdit"
                    name="country"
                    v-model="user.country"
                    label="name"
                    valueProp="iso2"
                    :options="countries"
                    placeholder="Select Country"
                    @change="onCountryChange"
                />
                <span v-else>{{ props.user.country || '-' }}</span>
            </div>
        </div>

        <!-- STATE -->
        <div class="row align-items-center mb-2">
            <div class="col-md-4 fw-semibold" v-translate>gbl.state</div>
            <div class="col-md-8 text-muted">
                <multiselect
                    v-if="isEdit"
                    v-model="user.state"
                    label="name"
                    valueProp="code"
                    :options="states"
                    placeholder="Select State"
                />
                <span v-else>{{ props.user.state || '-' }}</span>
            </div>
        </div>


        <!-- CITY -->
        <div class="row align-items-center mb-2">
            <div class="col-md-4 fw-semibold" v-translate>gbl.city</div>
            <div class="col-md-8 text-muted" v-if="cities?.length>0">
                <multiselect
                    v-if="isEdit"
                    name="city"
                    v-model="user.city"
                    label="name"
                    valueProp="name"
                    :options="cities"
                    placeholder="Select City"
                />
                <span v-else>{{ props.user.city || '-' }}</span>
            </div>
            <div v-else class="col-md-8 text-muted">
                <input-field v-if="isEdit" label="gbl.city" v-model="user.city" name="city"/>
                <span v-else>{{ props.user.city || '-' }}</span>
            </div>
        </div>

        <!-- ZIP -->
        <div class="row align-items-center mb-2">
            <div class="col-md-4 fw-semibold" v-translate>gbl.zip</div>
            <div class="col-md-8 text-muted">
                <input-field v-if="isEdit" v-model="user.zip_code" name="zip_code" />
                <span v-else>{{ props.user.zip_code || '-' }}</span>
            </div>
        </div>

        <!-- ADDRESS -->
        <div class="row align-items-center mb-2">
            <div class="col-md-4 fw-semibold" v-translate>gbl.address</div>
            <div class="col-md-8 text-muted">
                <Field as="textarea" v-if="isEdit" label="gbl.address" class="form-control form-control-sm" v-model="user.address" name="address" style="height: 60px"/>
                <span v-else>{{ props.user.address || '-' }}</span>
            </div>
        </div>

        <div v-if="isGoogleEmail"  class="row align-items-center mb-2">
            <div class="col-md-4 fw-semibold text-capitalize" v-translate>gbl.google.login</div>
            <div @click="changeGoogleAuth" class="col-md-8 text-muted apbd-pointer">  {{ props.user.is_sso=='Y' ? appsbdUtls.translateGettext('gbl.enable')  : appsbdUtls.translateGettext('gbl.disable') }}
                <button type="button" class="btn d-inline-flex justify-content-center align-items-center aspect-ratio-1x1 btn-sm rounded-circle btn-success "> <i class="apb apb-settings-041"></i> </button>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="row mt-4 g-2">
            <div class="col-12 col-md-6" v-if="!isEdit">
                <button class="btn btn-theme w-100" @click="startEdit" v-translate>update.profile</button>
            </div>

            <div class="col-12 col-md-6" v-if="isEdit">
                <button type="button" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" @click="updateProfile">
                    <translate>gbl.save</translate>
                    <small-loader v-if="isLoading" :show="isLoading" color="#FFF"/>
                </button>
            </div>

            <div class="col-12 col-md-6" v-if="isEdit">
                <button class="btn btn-outline-secondary w-100" @click="cancelEdit" v-translate>gbl.cancel</button>
            </div>

            <div class="col-12 col-md-6" v-if="!isEdit">
                <button class="btn btn-primary w-100" type="button" @click="openPasswordModal(props.user.id)" v-translate>change.password</button>
            </div>
        </div>
    </div>
    <google-auth-change :user_id="user.id" :is_sso="props.user.is_sso" @reload="emit('reload')" v-if="isShowModal" @close="isShowModal = false" />
</template>

<script setup>

import appsbdUtls from "../libs/AppsbdUtls.js";
import GoogleAuthChange from "@/modules/AdminPanel/User/GoogleAuthChange.vue";
import { reactive, ref, computed, onMounted, nextTick } from 'vue'
import Countries from '@/libs/countries.json'
import Multiselect from '@vueform/multiselect'
import { InputField, ApbdSwitchButton, ResponseMsg } from '@appsbd/vue3-appsbd-libs'
import ContactNumberInput from '@/components/ContactNumberInput.vue'
import { Field } from 'vee-validate'
import SmallLoader from '@/components/SmallLoader.vue'
import { useUserStore } from '@/modules/AdminPanel/User/UserStore.js'

const userStore = useUserStore()
const props = defineProps({
    user: { type: Object, required: true },
})


const isEdit = ref(false)
const countries = ref(Countries)
const isLoading = ref(false)
const msgs=ref({})
const user = reactive({
    name: '',
    username: '',
    email: '',
    contact_no: '',
    is_whatsapp: '',
    is_sso: '',
    country: '',
    state: '',
    city: '',
    zip_code: '',
    address: ''
})
const oldData = reactive({...user})

const syncUser = () => {
    Object.assign(user, props.user)
    Object.assign(oldData, props.user)
}
const emit = defineEmits(['openProfileModal', 'openPasswordModal','reload'])

onMounted(syncUser)

const isShowModal = ref(false)


const startEdit = async () => {
    syncUser()
    await nextTick()
    isEdit.value = true
}

const cancelEdit = async () => {
    syncUser()
    await nextTick()
    isEdit.value = false
}

function changeGoogleAuth() {
    isShowModal.value = true
}

const getChangedData = () => {
    const diff = {}
    for (const key in user) {
        if (user[key] !== oldData[key]) diff[key] = user[key]
    }
    return diff
    }
const updateProfile = async () => {

    const payload = getChangedData()
    if (!Object.keys(payload).length) {
        isEdit.value = false
        msgs.value = { error: ['No Change For Update'] }
        return
    }

    isLoading.value = true
    try {
        const response = await userStore.updateProfile({id:props.user.id, ...payload})
        msgs.value=response.msg

        // Make sure DOM updates happen after async update
        await nextTick()

        // Assign updated data
        Object.assign(props.user, response.data)
        Object.assign(user, response.data)
        isEdit.value = false
    } catch (e) {
        console.error(e)
    } finally {
        isLoading.value = false
    }
}

const onCountryChange = () => {
    user.state = ''
    user.city = ''
}

const states = computed(() => {
    const c = countries.value.find(i => i.iso2 === user.country)
    return c ? c.states : []
})

const isGoogleEmail = computed(() => {
    return props.user.email.toLowerCase().endsWith('@gmail.com');
});

const cities = computed(() => {
    const s = states.value.find(i => i.code === user.state)
    return s ? s.cities : []
})

const openPasswordModal = (id) => {
    emit('openPasswordModal', id)
}
</script>
