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
                <label class="form-label" v-translate>gbl.whatsapp</label>
                <div class="pt-1">
                    <apbd-switch-button
                        v-model="props.user.is_whatsapp"
                        name="is_whatsapp"
                        id="is_whatsapp"
                        title="is.whatsapp"
                        true-value="Y"
                        false-value="N"
                        container-class="mb-0"
                    />
                </div>
            </div>
        </div>

        <location-selector :modelValue="props.user"/>
    </div>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue'
import { AbInputField as InputField, AbMultiSelect as ApbdDropdown, AbToggle as ApbdSwitchButton } from '@appsbd/vue3-appsbd-ui'
import ContactNumberInput from '@/components/ContactNumberInput.vue'
import LocationSelector from "@/components/LocationSelector.vue";

import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
const loginStore = useLoginStore();

const props = defineProps({
    user: { type: Object, required: true },
    roles: { type: Array, default: () => [] }
})

const dropdownRoles = computed(() => {
    const list = (props.roles && props.roles.length > 0) ? props.roles : (loginStore.roleList || []);
    return list.map(({ id, title }) => ({
        val: Number(id),
        title
    }));
})

watch(() => props.user.role_id, (newVal) => {
    if (newVal !== null && newVal !== undefined && newVal !== '') {
        const num = Number(newVal);
        if (!isNaN(num) && props.user.role_id !== num) {
            props.user.role_id = num;
        }
    }
}, { immediate: true });

onMounted(async () => {
    if ((!props.roles || props.roles.length === 0) && (!loginStore.roleList || loginStore.roleList.length === 0)) {
        await loginStore.loadRoles();
    }
})

</script>

