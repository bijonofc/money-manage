<template>
  <div>
    <TabComponent />
    <div class="row row-cols-1" v-if="!isLoaded">
      <div class="col">
        <AppLoader />
      </div>
    </div>
    <div class="row g-4" v-else>
      <!-- Financial & App Settings -->
      <div class="col-12 col-lg-6">
        <form @submit.prevent="onGeneralSubmit" class="needs-validation">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
              <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <Wallet :size="20" class="text-primary" />
                Financial & Currency Settings
              </h5>
              <p class="text-muted small mb-0">Configure your default currency, organization name, and formatting</p>
            </div>
            <div class="card-body p-4">
              <div class="mb-3">
                <label class="form-label small fw-semibold">Application Name</label>
                <input v-model="general_settings.app_name" type="text" class="form-control" placeholder="Money Manage" />
              </div>

              <div class="row g-3 mb-3">
                <div class="col-6">
                  <label class="form-label small fw-semibold">Currency Symbol *</label>
                  <input v-model="general_settings.currency_symbol" type="text" class="form-control" placeholder="৳ or $" required />
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Currency Code *</label>
                  <input v-model="general_settings.currency_code" type="text" class="form-control" placeholder="BDT or USD" required />
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-6">
                  <label class="form-label small fw-semibold">Date Format</label>
                  <select v-model="general_settings.date_format" class="form-select">
                    <option value="YYYY-MM-DD">YYYY-MM-DD (2026-09-02)</option>
                    <option value="DD/MM/YYYY">DD/MM/YYYY (02/09/2026)</option>
                    <option value="MM/DD/YYYY">MM/DD/YYYY (09/02/2026)</option>
                    <option value="D MMM, YYYY">D MMM, YYYY (2 Sep, 2026)</option>
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Default Budget Alert (%)</label>
                  <input v-model.number="general_settings.budget_threshold" type="number" min="1" max="100" class="form-control" placeholder="80" />
                </div>
              </div>
            </div>
            <div class="card-footer bg-transparent border-0 d-flex justify-content-end pb-4 px-4 pt-0">
              <button class="btn btn-primary rounded-pill px-4" type="submit" :disabled="savingGeneral">
                {{ savingGeneral ? 'Saving...' : 'Save Settings' }}
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Alerts & Notification Settings -->
      <div class="col-12 col-lg-6">
        <form @submit.prevent="onNotificationSubmit" class="needs-validation">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
              <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <Bell :size="20" class="text-primary" />
                Notification & Alert Preferences
              </h5>
              <p class="text-muted small mb-0">Manage email notifications for budget limits and debt due dates</p>
            </div>
            <div class="card-body p-4">
              <div class="mb-4">
                <div class="form-check form-switch mb-2">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="enable_overbudget_alert"
                    v-model="notification_settings.overbudget_alerts"
                  />
                  <label class="form-check-label fw-semibold" for="enable_overbudget_alert">
                    Overbudget Warnings
                  </label>
                </div>
                <small class="text-muted d-block ps-4 ms-1">
                  Receive alerts when spending exceeds your defined category budget threshold.
                </small>
              </div>

              <div class="mb-4">
                <div class="form-check form-switch mb-2">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="enable_debt_reminders"
                    v-model="notification_settings.debt_reminders"
                  />
                  <label class="form-check-label fw-semibold" for="enable_debt_reminders">
                    Debt & Loan Payment Reminders
                  </label>
                </div>
                <small class="text-muted d-block ps-4 ms-1">
                  Receive email notifications 3 days before any upcoming debt or loan due dates.
                </small>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Alert Notification Email</label>
                <input
                  v-model="notification_settings.alert_email"
                  type="email"
                  class="form-control"
                  placeholder="yourname@example.com"
                />
              </div>
            </div>
            <div class="card-footer bg-transparent border-0 d-flex justify-content-end pb-4 px-4 pt-0">
              <button class="btn btn-primary rounded-pill px-4" type="submit" :disabled="savingNoti">
                {{ savingNoti ? 'Saving...' : 'Save Preferences' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useSettingStore } from '@/modules/AdminPanel/Settings/SettingStore.js';
import TabComponent from '@/modules/AdminPanel/Settings/TabComponent.vue';
import AppsbdUtls from '@/libs/AppsbdUtls.js';
import { AppLoader } from '@appsbd/vue3-appsbd-libs';
import { Wallet, Bell } from '@lucide/vue';

const isLoaded = ref(false);
const savingGeneral = ref(false);
const savingNoti = ref(false);
const settingStore = useSettingStore();

const general_settings = reactive({
  app_name: 'Money Manage',
  currency_symbol: '৳',
  currency_code: 'BDT',
  date_format: 'YYYY-MM-DD',
  budget_threshold: 80,
});

const notification_settings = reactive({
  overbudget_alerts: true,
  debt_reminders: true,
  alert_email: '',
});

const onGeneralSubmit = async () => {
  try {
    savingGeneral.value = true;
    const response = await settingStore.updateSettings({
      group_slug: 'general_settings',
      settings: { ...general_settings },
    });
    if (response) {
      AppsbdUtls.ShowServerResponseNotification(response.msg || 'Settings saved successfully', 3000);
    }
  } catch (e) {
    console.error(e);
  } finally {
    savingGeneral.value = false;
  }
};

const onNotificationSubmit = async () => {
  try {
    savingNoti.value = true;
    const response = await settingStore.updateSettings({
      group_slug: 'notification_settings',
      settings: { ...notification_settings },
    });
    if (response) {
      AppsbdUtls.ShowServerResponseNotification(response.msg || 'Notification preferences saved', 3000);
    }
  } catch (e) {
    console.error(e);
  } finally {
    savingNoti.value = false;
  }
};

onMounted(async () => {
  try {
    await settingStore.getSettings();
    if (settingStore.settingsList?.general_settings) {
      Object.assign(general_settings, settingStore.settingsList.general_settings);
    }
    if (settingStore.settingsList?.notification_settings) {
      Object.assign(notification_settings, settingStore.settingsList.notification_settings);
    }
  } catch (e) {
    console.error(e);
  } finally {
    isLoaded.value = true;
  }
});
</script>

<style scoped lang="scss">
</style>
