<template>
    <div class="inline-edit-field d-flex align-items-start justify-content-between flex-wrap py-2">

        <label class="fw-medium me-2 mb-1 mb-sm-0">
            <translate>{{ label }}</translate>:
        </label>

        <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end">
            <template v-if="!editing">
            <span class="fw-semibold text-break text-secondary">
              {{ getDisplayValue(modelValue) || placeholder }}
            </span>
                <i class="apb apb-edit-01 text-success edit-icon" @click="startEdit"></i>
            </template>
            <template v-else>
                <div class="edit-input-wrapper flex-grow-1">
                    <slot name="editor" :modelValue="internalValue" @update:modelValue="(val) => (internalValue = val)">
                        <template v-if="type === 'textarea'">
                             <textarea class="form-control form-control-sm" rows="3" :placeholder="placeholder" v-model="internalValue"></textarea>
                        </template>
                        <template v-else>
                            <input :type="type" v-model="internalValue" class="form-control form-control-sm" :placeholder="placeholder"/>
                        </template>
                    </slot>
                </div>

                <div class="action-icons d-flex align-items-center gap-1">
                    <i v-if="!isShowLoader" class="apb apb-circle-check text-success action-icon" @click="saveEdit"></i>
                    <small-loader v-if="isShowLoader" :show="isShowLoader" color="#28a745" size="6"/>
                    <i class="apb apb-x-circle text-danger action-icon" @click="cancelEdit"></i>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import SmallLoader from "@/components/SmallLoader.vue";
import { useTempCustomerStore } from "@/modules/AdminPanel/TempCustomer/TempCustomerStore.js";
import AppsbdUtls from "@/libs/AppsbdUtls.js";

const tempStore = useTempCustomerStore();

const props = defineProps({
    modelValue: [String, Number, Boolean, Object],
    label: { type: String, required: true },
    placeholder: { type: String, default: "N/A" },
    type: { type: String, default: "text" },
    prop_key: { type: String, default: "" },
    edit_id: { type: Number, default: null },
});

const emit = defineEmits(["update:modelValue", "save"]);

const editing = ref(false);
const internalValue = ref(props.modelValue);
const isShowLoader = ref(false);

const startEdit = () => {
    internalValue.value = props.modelValue;
    editing.value = true;
};

const cancelEdit = () => {
    editing.value = false;
};

const saveEdit = async () => {
    isShowLoader.value = true;
    try {
        const response = await tempStore.updateCustomer({
            id: props.edit_id,
            field: props.prop_key,
            value: internalValue.value,
        });

        if (response.status) {
            editing.value = false;
            emit("update:modelValue", internalValue.value);
        }

        AppsbdUtls.ShowServerResponseNotification(response.msg, 5000);
    } catch (e) {
        AppsbdUtls.ShowServerResponseNotification({ errors: [e.message] }, 5000);
    } finally {
        isShowLoader.value = false;
    }
};
const getDisplayValue = (val) => {
    switch (props.prop_key) {
        case "shop_type":
            if (val === "R") return AppsbdUtls.translateGettext('register.res');
            if (val === "G") return AppsbdUtls.translateGettext('register.gro');
            if (val === "P") return AppsbdUtls.translateGettext('register.pay');
            if (val === "B") return AppsbdUtls.translateGettext('register.bas');
            return "-";

        case "is_whatsapp":
            if (val === "Y" || val === true) return "Yes";
            if (val === "N" || val === false) return "No";
            return "-";

        default:
            return val;
    }
};
watch(
    () => props.modelValue,
    (val) => (internalValue.value = val)
);
</script>

<style scoped lang="scss">
.inline-edit-field {
    .edit-icon,
    .action-icon {
        font-size: .98rem;
        cursor: pointer;

    }

    .edit-icon:hover {
        color: #28a745 !important;

    }



    //.edit-input-wrapper {
    //    min-width: 150px;
    //    max-width: 250px;
    //}

    @media (max-width: 576px) {
        //flex-direction: column;

        .edit-input-wrapper {
            width: 100%;
            max-width: 100%;
        }

        .action-icons {
            margin-top: 0.3rem;
        }
    }
}
</style>
