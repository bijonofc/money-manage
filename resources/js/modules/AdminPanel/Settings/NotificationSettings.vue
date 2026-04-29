<template>
    <TabComponent />

    <div class="row row-cols-1 row-cols-sm-2 g-3">
        <div class="col">
            <SettingsForm :on-submit="onSubmit" class="needs-validation">
                <div class="card apbd-theme-card h-100">
                    <div class="card-header">
                        <h5 class="card-title" v-translate>settings.noti</h5>
                    </div>
                    <div class="card-body apbd-loading-target p-3 o-unset">
                        <div class="row row-cols-1 row-cols-sm-2">
                            <div class="col">
                            <label for="start_day" class="form-label" v-translate>sprint.start</label>
                            <Field as="select" name="start_day" label="sprint.start" class="form-select form-select-sm" v-model="sprint_settings['start_day']">
                                <option value='' disabled v-translate>select.start</option>
                                <option v-for="df in days" :key="df.code" :value="df.code">
                                    {{ df.name }}
                                </option>
                            </Field>
                        </div>
                            <div class="col">
                                <label for="start_day" class="form-label" v-translate>sprint.dd</label>
                                <div class="input-group input-group-sm mb-3">
                                    <Field class="form-control form-control-sm" type="number" name="default_duration" v-model="sprint_settings.default_duration" label="sprint.dd" min="1"  rules="required|min_value:1">
                                    </Field>
                                    <span class="input-group-text" id="basic-addon2" v-translate>sprint.weeks</span>
                                </div>

                            </div>

                            <div class="col">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="allow_backlog_issues" true-value="Y" false-value="N" v-model="sprint_settings.allow_backlog_issues">
                                    <label class="form-check-label" for="allow_backlog_issues" v-translate>sprint.allow.bi</label>
                                </div>
                            </div>
                                <div class="col">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="auto_close_sprint" true-value="Y" false-value="N" v-model="sprint_settings.auto_close_sprint">
                                        <label class="form-check-label" for="auto_close_sprint" v-translate>sprint.auto.cs</label>
                                    </div>
                                </div>


                            <div class="col">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="carry_over_incomplete" true-value="Y" false-value="N" v-model="sprint_settings.carry_over_incomplete">
                                    <label class="form-check-label" for="carry_over_incomplete" v-translate>sprint.carry.oi</label>
                                </div>
                            </div>
                                <div class="col">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="sprint_review_required" true-value="Y" false-value="N" v-model="sprint_settings.sprint_review_required">
                                        <label class="form-check-label" for="sprint_review_required" v-translate>sprint.srr</label>
                                    </div>
                                </div>



                        </div>




                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <button class="btn btn-sm btn-theme" type="submit" v-translate>gbl.save</button>
                    </div>
                </div>
            </SettingsForm>
        </div>
    </div>
</template>

<script setup>
import { reactive, onMounted } from 'vue'
import { Field, ErrorMessage } from 'vee-validate'
import { useSettingStore } from '@/modules/AdminPanel/Settings/SettingStore.js'
import TabComponent from '@/modules/AdminPanel/Settings/TabComponent.vue'
import { SettingsForm,InputField,ApbdDatePicker, } from '@appsbd/vue3-appsbd-libs'
import AppsbdUtls from '@/libs/AppsbdUtls.js'

const settingStore = useSettingStore()

const sprint_settings = reactive({
    default_duration: 14,
    start_day: 'Monday',
    allow_backlog_issues: false,
    auto_close_sprint: false,
    carry_over_incomplete: true,
    sprint_review_required: true,
})
const days = [
    { code: 'mon', name: 'Monday' },
    { code: 'tue', name: 'Tuesday' },
    { code: 'wed', name: 'Wednesday' },
    { code: 'thu', name: 'Thursday' },
    { code: 'fri', name: 'Friday' },
    { code: 'sat', name: 'Saturday' },
    { code: 'sun', name: 'Sunday' },
];
const onSubmit = async () => {
    const response = await settingStore.updateSettings({
        group_slug: 'sprint_settings',
        settings: { ...sprint_settings }
    })
    if (response) {
        AppsbdUtls.ShowServerResponseNotification(response.msg || 'Sprint settings saved', 5000)
    }
}

onMounted(async () => {
    await settingStore.getSettings()
    Object.assign(sprint_settings, settingStore.settingsList.sprint_settings || {})
})
</script>

<style scoped lang="scss">
/* Optional: add spacing and card style if needed */
</style>
