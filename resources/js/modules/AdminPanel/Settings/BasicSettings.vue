<template>
    <TabComponent />
    <div class="row row cols-1" v-if="!isLoaded">
        <div class="col">
            <AppLoader  />
        </div>
    </div>
    <div class="row row-cols-2" v-else>
<!--        <InvoiceTemplate />-->
        <div class="col">
            <SettingsForm :on-submit="onSubmit" class="needs-validation">
                        <div class="card apbd-theme-card h-100">
                            <div class="card-header">
                                <h5 class="card-title" v-translate>Amount Settings</h5>
                            </div>
                            <div class="card-body apbd-loading-target p-3 o-unset">
                                <div class="row row-cols-1 row-cols-sm-2 g-3">
                                    <div class="col">
                                        <div class="mb-2 ">
                                                <input-field
                                                    label="settings.min.withdraw"
                                                    class="form-control text-end"
                                                    container-class="mb-0"
                                                    v-model="amount_settings.min_withdraw_amount"
                                                    name="min_amount"
                                                    rules="required|min_value:0",
                                                    type="number"
                                                />

                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-2 ">
                                            <input-field
                                                label="settings.settle.days"
                                                class="form-control text-end"
                                                container-class="mb-0"
                                                v-model="amount_settings.settle_days"
                                                name="settle_days"
                                                rules="required|min_value:0",
                                                type="number"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-cols-1 row-cols-sm-2 g-3">
                                    <div class="col">
                                        <div class="mb-2 ">
                                                <input-field
                                                    label="settings.min.topup"
                                                    class="form-control text-end"
                                                    container-class="mb-0"
                                                    v-model="amount_settings.min_topup_amount"
                                                    name="min_topup_amount"
                                                    rules="required|min_value:0",
                                                    type="number"
                                                />

                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cols-1 row-cols-sm-2 g-3 mt-3">
                                    <div class="col">
                                        <div class="mb-2 ">
                                            <div class="mb-3 d-flex justify-content-between justify-content-sm-start align-items-start">
                                                <div class="form-check form-switch form-switch-sm mt-0 ">
                                                    <input class="form-check-input me-3" v-model="amount_settings.is_single_withdraw" type="checkbox"
                                                           true-value="Y"
                                                           false-value="N"
                                                           id="is_email_completed_by"
                                                           name="is_email_completed_by">
                                                </div>
                                                <label for="is_email_completed_by" class="label me-2">
                                                    <div v-translate>settings.single.withdraw.req</div>
                                                    <small class="help-text text-muted" v-translate>Enable this option to allow only one withdrawal request per agent.</small>
                                                </label>
                                            </div>
                                            <!--                                            <apbd-switch-button tr v-model="amount_settings.is_single_withdraw" id="is_extended" container-class="form-switch-md">
                                                                                            <template #topLabel>
                                                                                                <translate>
                                                                                                    settings.single.withdraw
                                                                                                </translate>
                                                                                            </template>
                                                                                        </apbd-switch-button>-->
                                            <!--                                                <input-field
                                                                                                label="stngs.min.amount"
                                                                                                class="form-control text-end"
                                                                                                container-class="mb-0"
                                                                                                v-model="amount_settings.is_single_withdraw"
                                                                                                name="min_amount"
                                                                                                rules="required|min_value:1",
                                                                                                type="number"
                                                                                            />-->

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <button v-if="$CheckACL('np.setting-add')" class="btn btn-sm btn-theme" type="submit" v-translate>gbl.save</button>
                            </div>
                        </div>
                    </SettingsForm>
        </div>

        <div class="col">
            <div class="row row-cols-1">
                <div class="col mb-4">
                    <settings-form :on-submit="onAuthSubmit" class="needs-validation ">
                        <div class="card apbd-theme-card">
                            <div class="card-header">
                                <h5 class="card-title" v-translate>settings.noti</h5>
                            </div>
                            <div class="card-body apbd-loading-target p-3 o-unset">
                                <div class="row row-cols-1 row-cols-sm-2 g-3">
                                    <div class="col">
                                        <div class="mb-2">
                                            <div class="form-check form-switch form-switch-sm">
                                                <label class="form-check-label" for="enable_mail" v-translate>
                                                    settings.mail.enable
                                                </label>
                                                <input class="form-check-input me-2" type="checkbox" true-value="Y" false-value="N" v-model="notification_settings.enable_mail" role="switch" id="enable_mail">
                                            </div>
                                        </div>
                                        <div v-if="notification_settings.enable_mail=='Y'">
                                            <label class="form-label" v-translate>settings.mail.to</label>
                                            <Field  id="mail_to" label="mail_to" rules="required" class="form-control form-control-sm" name="mail_to" v-model="notification_settings.mail_to" />
                                            <ErrorMessage name="mail_to" class="apbd-v-error"/>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-2">
                                            <div class="form-check form-switch form-switch-sm">
                                                   <label class="form-check-label" for="enable_discord" v-translate>
                                                        settings.discord.enable
                                                    </label>
                                                    <input class="form-check-input me-2" type="checkbox" true-value="Y" false-value="N" v-model="notification_settings.enable_discord" role="switch" id="enable_discord">
                                            </div>
                                        </div>
                                        <div v-if="notification_settings.enable_discord=='Y'">
                                            <label class="form-label" v-translate>settings.discord.to</label>
                                            <Field  id="discord_to" label="discord_to" rules="required" class="form-control form-control-sm" name="discord_to" v-model="notification_settings.discord_to" />
                                            <ErrorMessage name="discord_to" class="apbd-v-error"/>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <button v-if="$CheckACL('np.setting-add')" class="btn btn-sm btn-theme" type="submit" v-translate>gbl.save</button>
                            </div>
                        </div>
                    </settings-form>
                </div>

            </div>


        </div>
    </div>
</template>
<script setup>
import { ref, reactive, onMounted } from 'vue'

import { Field, ErrorMessage } from 'vee-validate'

import {useSettingStore} from '@/modules/AdminPanel/Settings/SettingStore.js'


import TabComponent from '@/modules/AdminPanel/Settings/TabComponent.vue'

import AppsbdUtls from '@/libs/AppsbdUtls.js'
import {SettingsForm, AppLoader, InputField, ApbdSwitchButton} from '@appsbd/vue3-appsbd-libs'
import InvoiceTemplate from "@/components/InvoiceTemplate.vue";


const isLoaded=ref(false);

const settingStore=useSettingStore();
const invoice_settings = reactive({

});

const notification_settings = reactive({

});

const settings = ref({min_withdraw_amount:''})
const amount_settings = reactive({min_withdraw_amount:'',is_single_withdraw:'N'})


const onSubmit= async ()=>{
    let response = await settingStore.updateSettings(
        {
            group_slug:'amount_settings',
            settings: {...amount_settings}
        });
    if (response) {
        AppsbdUtls.ShowServerResponseNotification(response.msg, 5000);
    }
}
const onAuthSubmit = async () => {
    let response = await settingStore.updateSettings({
        group_slug: "notification_settings",
        settings: { ...notification_settings },
    });

    if (response) {
        AppsbdUtls.ShowServerResponseNotification(response.message || response.msg, 5000);
    }
};

onMounted(async () => {
    await settingStore.getSettings();
    Object.assign(amount_settings, settingStore.settingsList.amount_settings || {});
    isLoaded.value=true;
});

</script>

<style scoped lang="scss">


</style>
