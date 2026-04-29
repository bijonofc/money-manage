<template>
    <modal
        :modal-msg="msg"
        modal-size="modal-xl"
        ref="emailTemplateModal"
        @onSubmit="updateTemplate"
        @loading-status="loaderStatusChange"
        @close="emitClose"
    >
        <!-- Header -->
        <template #header>
            <span>
                {{AppsbdUtls.translateGettext('gbl.edit',{name:'template'})}}
            </span>

        </template>

        <!-- Body -->
        <template #body>
            <div class="row g-3">
                <div class="col-sm-12 col-lg-7">
                    <div class="mb-3">
                        <label class="form-label" for="email_subject" v-translate>gbl.subject</label>
                        <Field
                            label="gbl.subject"
                            type="text"
                            v-model="templateInfo.subject"
                            rules="required"
                            name="email_subject"
                            id="email_subject"
                            class="form-control form-control-sm form-control-md"
                        />
                        <ErrorMessage name="email_subject" class="apbd-v-error" />
                    </div>

                    <div>
                        <label for="email_content" class="form-label" v-translate>gbl.body</label>
                        <div class="apd-param-same-height">
                            <QuillEditor id="email_content" v-model:content="templateInfo.content" content-type="html" ref="templateEditor" />
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-lg-5">
                    <template-props :template-properties="emailProps" @insetText="insertIntoEmailTemplate" />
                </div>
            </div>
        </template>

        <template #footer>
            <button class="btn btn-sm btn-danger"  @click="emitClose" v-translate>
                gbl.close
            </button>
            <button type="submit" v-if="$CheckACL('np.template-update')"  class="btn btn-sm btn-primary" v-translate>
                gbl.save.changes
            </button>
        </template>
    </modal>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Field, ErrorMessage, Form } from 'vee-validate'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import TemplateProps from '@/modules/AdminPanel/EmailTemplate/TemplateProps'
import { Modal } from '@appsbd/vue3-appsbd-libs'
import {useTemplateStore} from '@/modules/AdminPanel/EmailTemplate/TemplateStore.js'
import AppsbdUtls from "@/libs/AppsbdUtls.js";
import appsbdUtls from "@/libs/AppsbdUtls.js";

const templateStore=useTemplateStore();

// Props & Emits
const props = defineProps({
    key_word: {
        type: String,
        default: null,
    },
})
const emit = defineEmits(['close', 'reload'])
const emailTemplateModal = ref(null)
const templateEditor = ref(null)
const templateEditor2 = ref(null)


const msg = ref({})
const templateInfo = reactive({
    k_word: '',
    subject: '',
    content: '',
})
const oldTempInfo = reactive({})
const emailProps = ref({  })
const isShowLoader = ref(false)

onMounted(() => {
    getData()
})


const getData = async () => {
    if (props.key_word !== null) {
        emailTemplateModal.value.showLoader(true,appsbdUtls.translateGettext('Loading type', { type: 'Email Template' }) )
        const response = await templateStore.getEmailTemplate({
            key: props.key_word,
        })
        if (response.status) {
            Object.assign(templateInfo, response.data)
            Object.assign(oldTempInfo, response.data)
            emailProps.value = response.data?.props || []
        }


        emailTemplateModal.value.showLoader(false)

    }
}

const loaderStatusChange = (param) => {
    isShowLoader.value = param
}

const insertIntoEmailTemplate = (text) => {
    insertText('templateEditor', text)
}

const insertText = (refId, text) => {
    const quill = templateEditor.value.getQuill()
    quill.focus()
    const caret = quill.getSelection(true)
    quill.insertText(caret, text)
}
const getFormData = () => {
    if (props.key_word != null) {
        const diff = {}
        for (const key in templateInfo) {
            if (templateInfo[key] !== oldTempInfo[key]) diff[key] = templateInfo[key]
        }
        return diff
    }
    return { ...templateInfo }
}
const updateTemplate = async () => {
    const formData = getFormData()

    if (props.key_word !== null) {
        if (Object.keys(formData).length === 0) {
            msg.value = { error: ['No change found for update'] }
            return
        }

        formData.k_word = props.key_word
        emailTemplateModal.value.showLoader(true, 'Updating Email Template')

        const response = await templateStore.updateEmailTemplate({ ...formData })
        msg.value = response.msg

        if (response.status) {
            emailTemplateModal.value.showMsgOnly(response.msg, response.status)
            Object.assign(templateInfo, { k_word: '', subject: '', content: '' })
            Object.assign(oldTempInfo, {})
            emailProps.value = []
            emit('reload')
        } else {
            emailTemplateModal.value.showMsgOnly(response.msg, response.status)
        }

        emailTemplateModal.value.showLoader(false)
    }
}
const emitClose = () => {
    emit('close')
}
</script>

<style>
.ql-snow .ql-editor a {
    text-decoration: none !important;
}
.apd-param-same-height {
    height: calc(100% - 66px);
}
</style>
