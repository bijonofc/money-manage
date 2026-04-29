<template>
    <div>
        <div class="row row-cols-1 row-cols-sm-2 g-3">
            <div class="col">
                <input-field label="gbl.name" v-model="props.user.name" name="name" rules="required"/>
            </div>
            <div class="col">
                <input-field label="gbl.username" v-model="props.user.username" name="username" rules="required"/>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 g-3">
            <div class="col">
                <input-field label="gbl.email" v-model="props.user.email" name="email" rules="required|email"/>
            </div>
            <div class="col">
                <apbd-dropdown label="gbl.role" v-model="props.user.role_id" name="role_id" :options="dropdownRoles" placeholder="Select" rules="required"/>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 g-3 mb-3">
            <div class="col">
                <contact-number-input v-model="props.user.contact_no" label="gbl.contact.no" rules="required"/>
            </div>
            <div class="col">
                <apbd-switch-button v-model="props.user.is_whatsapp" id="is_whatsapp" container-class="form-switch-sm">
                    <template #topLabel>
                        <translate>is.whatsapp</translate>
                    </template>
                </apbd-switch-button>
            </div>
        </div>

        <location-selector :modelValue="props.user"/>
    </div>
</template>

<script setup>
import { computed, onMounted} from 'vue'
import { InputField, ApbdDropdown, ApbdSwitchButton} from '@appsbd/vue3-appsbd-libs'
import ContactNumberInput from '@/components/ContactNumberInput.vue'
import LocationSelector from "@/components/LocationSelector.vue";

import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
const loginStore = useLoginStore();

const props = defineProps({
    user: { type: Object, required: true }
})


const dropdownRoles = computed(() =>
    loginStore.roleList.map(({ id, title }) => ({
        val: id,
        title
    }))
)

onMounted(()=>{

})

</script>

