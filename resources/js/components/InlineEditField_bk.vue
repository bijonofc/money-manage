<template>
    <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex align-items-center justify-content-between">
            <!-- Label -->
            <span class="fw-medium me-2"><translate>{{ label }}</translate>:</span>


            <div class="d-flex align-items-center gap-2">

                <template v-if="!editing">
                    <span class="text-truncate fw-semibold ">{{ modelValue || placeholder }}</span>
                    <i class="apb apb-edit-01 text-success" style="cursor:pointer" @click="startEdit"></i>
                </template>

                <!-- Edit Mode -->
                <template v-else>
                    <div class="">
                        <slot name="editor" :modelValue="internalValue" @update:modelValue="val => internalValue = val">
                            <template v-if="type === 'textarea'">
                                <textarea class="form-control form-control-sm" rows="3" :placeholder="placeholder" v-model="internalValue"/>
                            </template>
                            <template v-else>
                                <input :type="type" v-model="internalValue" class="form-control form-control-sm" :placeholder="placeholder"/>
                            </template>
                        </slot>
                    </div>
                    <span class="" @click="saveEdit">
                        <i class="apb apb-circle-check text-success" @click="saveEdit" style="cursor:pointer"></i>
                        <small-loader v-if="isShowLoader"  :show="isShowLoader" color="#ece" size="5" />
                    </span>
                    <span class=""  @click="cancelEdit">
                        <i class="apb apb-x-circle text-danger" @click="cancelEdit" style="cursor:pointer"></i>
                    </span>

                </template>
            </div>
        </li>
    </ul>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Field } from 'vee-validate';
import SmallLoader from "@/components/SmallLoader.vue";
import {useTempCustomerStore} from "@/modules/AdminPanel/TempCustomer/TempCustomerStore.js";
import AppsbdUtls from "@/libs/AppsbdUtls.js";
const tempStore=useTempCustomerStore();
const props = defineProps({
    modelValue: [String, Number, Boolean, Object],
    label: { type: String, required: true },
    placeholder: { type: String, default: 'N/A' },
    type: { type: String, default: 'text' },
    prop_key: { type: String, default: '' },
    edit_id: { type: Number, default: null },

});

const emit = defineEmits(['update:modelValue', 'save']);
const editing = ref(false);
const internalValue = ref(props.modelValue);
const isShowLoader=ref(false);
const startEdit = () => {
    internalValue.value = props.modelValue;
    editing.value = true;
};
const cancelEdit = () => editing.value = false;
const saveEdit = async() => {
    isShowLoader.value = true;
    try {
        console.log({id:props.edit_id, field:props.prop_key, value:internalValue.value});
        console.log({id:props.edit_id, field:props.prop_key, value:internalValue.value});
        const response = await tempStore.updateCustomer({id:props.edit_id, field:props.prop_key, value:internalValue.value});
        console.log(response);
        if(response.status) {
            editing.value = false;
        }
        AppsbdUtls.ShowServerResponseNotification(response.msg, 5000);
    } catch(e) {
        console.log(e);
        AppsbdUtls.ShowServerResponseNotification({errors:[e.message]}, 5000);
    }

    isShowLoader.value = false;
};

const showLoader=(val)=>{
    isShowLoader.value = true;
}

// Keep internal value synced with parent
watch(() => props.modelValue, (val) => internalValue.value = val);
</script>

<style scoped lang="scss">
.list-group-item {
    font-size: 0.95rem;
    background-color: transparent;
    border: none;
    padding: 0.4rem 0;
    display: flex;
    justify-content: space-between;
    align-items: center;

    .form-control-sm,
    .multiselect {
        width: 100%;
    }

    i.apb-edit-01,
    i.apb-circle-check,
    i.apb-x-circle {
        cursor: pointer;
    }
}
</style>
