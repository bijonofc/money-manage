<template>
    <div
        class="inline-edit-field d-flex align-items-sm-center flex-md-row flex-column justify-content-sm-between align-items-start  py-2">

        <label v-if="label" class="fw-medium me-2 mb-1 mb-sm-0">
            <translate>{{ label }}</translate>:
        </label>

        <div class="d-flex align-items-center gap-2  justify-content-sm-end content w-100">
            <template v-if="!editing">
                <span class="fw-semibold text-break text-secondary">
                    {{ getDisplayValue(modelValue) || placeholder }}
                </span>
                <i v-if="!disabled && $CheckACL('np.customer-update')" class="apb apb-pen-square text-primary edit-icon"
                    @click="startEdit"></i>
            </template>
            <template v-else class="w-100">

                <div class="edit-input-wrapper flex-grow-1 w-100">
                    <slot name="editor" :modelValue="internalValue" :updateModelValue="(val) => (internalValue = val)">
                        <template v-if="type === 'textarea'">
                            <textarea class="form-control form-control-sm" rows="3" :placeholder="placeholder"
                                v-model="internalValue"></textarea>
                        </template>
                        <template v-else>
                            <input :type="type" v-model="internalValue" class="form-control form-control-sm"
                                :placeholder="placeholder" />
                        </template>
                    </slot>
                </div>



                <div class="action-icons d-flex align-items-center gap-1" v-if="$CheckACL('np.customer-update')">
                    <i v-if="!isShowLoader" class="apb apb-circle-check text-success action-icon" @click="saveEdit"></i>
                    <small-loader v-if="isShowLoader" :show="isShowLoader" color="#28a745" size="6" />
                    <i class="apb apb-x-circle text-danger action-icon" @click="cancelEdit"></i>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import SmallLoader from "@/components/SmallLoader.vue";
import AppsbdUtls from "@/libs/AppsbdUtls.js";
import { useLoginStore } from "@/modules/AdminPanel/User/loginStore.js";
import { useCustomerStore } from "@/modules/AdminPanel/Customer/CustomerStore.js";
import appHelper from "@/libs/AppHelper.js";
const tempStore = useTempCustomerStore();
const loginStore = useLoginStore();
const customerStore = useCustomerStore();

const props = defineProps({
    modelValue: [String, Number, Boolean, Object],
    label: { type: String, required: true },
    placeholder: { type: String, default: "N/A" },
    type: { type: String, default: "text" },
    prop_key: { type: String, default: "" },
    edit_id: { type: Number, default: null },
    disabled: { type: Boolean, default: false },
    update_from: { type: String, default: "temp" }
});

const emit = defineEmits(["update:modelValue", "save"]);

const editing = ref(false);
const internalValue = ref(props.modelValue);
const isShowLoader = ref(false);

const startEdit = () => {
    if (props.disabled) return;
    internalValue.value = props.modelValue;
    editing.value = true;
    emit("edit-start");
};

const cancelEdit = () => {
    editing.value = false;
    emit("edit-end");
};

const saveEdit = async () => {
    isShowLoader.value = true;
    let response;
    try {
        if (props.update_from == "temp") {

            response = await tempStore.updateCustomer({
                id: props.edit_id,
                field: props.prop_key,
                value: internalValue.value,
            });
        }

        else if (props.update_from == "customer") {

            response = await customerStore.updateCustomer({
                id: props.edit_id,
                field: props.prop_key,
                value: internalValue.value,
            });
        }



        if (response.status) {
            editing.value = false;
            emit("edit-end");
            emit("update:modelValue", internalValue.value);
            if (props.prop_key == "pkg_id") {
                const pkg = loginStore.packageList.find(p => p.id == internalValue.value);
                if (pkg) {
                    emit("update-pkg-info", { price: parseFloat(pkg.price) });
                }
            }
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
        case "pkg_id":
            const pkg = loginStore.packageList.find(p => p.id === val);
            return pkg ? pkg.name : "-";
        case "is_whatsapp":
            if (val === "Y" || val === true) return "Yes";
            if (val === "N" || val === false) return "No";
            return "-";
        case "next_bill_date":
            return appHelper.formatDate(val);

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
        color: #2e3fa7 !important;

    }



    //.edit-input-wrapper {
    //    min-width: 150px;
    //    max-width: 250px;
    //}

    @media (max-width: 576px) {
        .content {
            width: 100%;
        }

        //flex-direction: column;

        //.edit-input-wrapper {
        //    width: 100%;
        //    max-width: 100%;
        //}
        //
        //.action-icons {
        //    margin-top: 0.3rem;
        //}
    }

}
</style>
