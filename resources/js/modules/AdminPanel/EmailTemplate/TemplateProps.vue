<template>
    <div class="card rp-card">
        <div class="card-body p-0">
            <table class="table m-0">
                <thead>
                <tr>
                    <th v-translate>gbl.properties</th>
                    <th v-translate>gbl.description</th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="Object.keys(templateProperties).length === 0">
                    <td colspan="2" class="text-center text-danger">No Property defined</td>
                </tr>

                <tr
                    v-for="(description, prop) in templateProperties"
                    :key="prop"
                >
                    <td width="50%" @click="insertText(prop)" class="prop-ctnr">
                        <i class="apb apb-arrow-left me-1"></i>
<!--                        <svg width="20" height="20" viewBox="0 0 20 20">-->
<!--                            <use href="#icon-inset-html" />-->
<!--                        </svg>-->
                        {{ getProp(prop) }}
                        <i class="ms-1 apb apb-file-copy" @click.stop="copy(prop)"></i>
                    </td>
                    <td class="text-sm">
                        <small>{{ description }}</small>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
    templateProperties: {
        type: Object,
        default: () => ({}),
    },
})

const emit = defineEmits(['insetText'])

const getProp = (prop) => `{{${prop}}}`

const insertText = (prop) => {
    emit('insetText', `{{${prop}}}`)
}
const copy = async (prop) => {
    try {
        await navigator.clipboard?.writeText(getProp(prop))
        console.log('copied');
        console.log(getProp(prop));
    } catch (err) {
        console.error('Failed to copy!', err)
    }
}
</script>

<style lang="scss" scoped>
.rp-card {
    padding: 2px !important;
    border-color: var(--bs-card-border-color, #eeeeee) !important;
}
.prop-ctnr {

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #303030;
    cursor: pointer;
    > svg {
        color: #707070;
    }
    &:hover {
        &,
        * {
            color: #3c701e;
        }
    }
}
table.table {
    tr {
        &:last-child {
            border-color: #ffffff;
        }
        td {
            vertical-align: middle;
        }
    }
}
</style>
