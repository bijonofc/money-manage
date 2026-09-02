<template>
  <div class="accounts-page pb-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
          <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
            <Wallet :size="24" class="text-primary" />
            Accounts & Wallets
          </h4>
          <p class="text-muted small mb-0">Manage bank accounts, cash in hand, mobile money, and credit cards</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2" @click="loadAccounts">
            <RefreshCw :size="15" :class="{ 'spin-anim': loading }" />
            <span>Refresh</span>
          </button>
          <button class="btn btn-primary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-sm" @click="openCreateModal">
            <Plus :size="16" />
            <span>New Account</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Accounts Cards Grid -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else-if="accounts.length === 0" class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
      <Wallet :size="48" class="mx-auto mb-3 opacity-50" />
      <h5>No accounts found</h5>
      <p class="small mb-4">Add your first bank account, credit card, or cash wallet to begin tracking.</p>
      <div>
        <button class="btn btn-primary rounded-pill px-4 py-2" @click="openCreateModal">
          Create Account
        </button>
      </div>
    </div>

    <div v-else class="row g-3">
      <div v-for="acc in accounts" :key="acc.id" class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 account-item-card p-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-3">
              <div class="acc-icon-box rounded-3 p-3 d-flex align-items-center justify-content-center" :class="getAccClass(acc.account_type)">
                <CreditCard v-if="acc.account_type === 'credit_card'" :size="22" />
                <Wallet v-else-if="acc.account_type === 'cash'" :size="22" />
                <Building2 v-else-if="acc.account_type === 'bank'" :size="22" />
                <Smartphone v-else-if="acc.account_type === 'mobile'" :size="22" />
                <Layers v-else :size="22" />
              </div>
              <div>
                <h6 class="fw-bold mb-0 text-dark">{{ acc.name }}</h6>
                <span class="badge bg-secondary-subtle text-secondary small text-capitalize">
                  {{ acc.account_type.replace('_', ' ') }}
                </span>
              </div>
            </div>
            <div class="dropdown">
              <button class="btn btn-icon btn-light btn-sm rounded-circle" @click="openEditModal(acc)">
                <Pencil :size="15" class="text-secondary" />
              </button>
              <button class="btn btn-icon btn-light btn-sm rounded-circle ms-1" @click="deleteAccount(acc.id)">
                <Trash2 :size="15" class="text-danger" />
              </button>
            </div>
          </div>

          <div class="mt-2">
            <span class="text-muted small">Current Balance</span>
            <h4 class="fw-bold mb-0 text-dark">
              {{ currencySymbol }}{{ formatNumber(acc.balance) }}
            </h4>
            <div v-if="acc.account_number" class="text-muted small mt-2">
              <span class="opacity-75">A/C:</span> {{ acc.account_number }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Redesigned Account Modal -->
    <AccountFormModal
      v-model="showModal"
      :edit-data="selectedAccount"
      :saving="saving"
      @save="saveAccount"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js';
import AppsbdURL from '@/libs/AppsbdURL.js';
import AppsbdUtls from '@/libs/AppsbdUtls.js';
import AccountFormModal from './AccountFormModal.vue';

import {
  Wallet,
  Building2,
  Smartphone,
  CreditCard,
  Layers,
  Plus,
  Pencil,
  Trash2,
  RefreshCw,
} from '@lucide/vue';

const accounts = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const selectedAccount = ref(null);

const currencySymbol = computed(() => window.app_settings?.currencySymbol || '৳');

function getAccClass(type) {
  switch (type) {
    case 'cash': return 'bg-success-subtle text-success';
    case 'credit_card': return 'bg-warning-subtle text-warning';
    case 'mobile': return 'bg-info-subtle text-info';
    case 'bank': return 'bg-primary-subtle text-primary';
    default: return 'bg-secondary-subtle text-secondary';
  }
}

function formatNumber(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

async function loadAccounts() {
  try {
    loading.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('accounts/list'), {});
    if (res?.data?.rowdata) {
      accounts.value = res.data.rowdata;
    }
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

function openCreateModal() {
  selectedAccount.value = null;
  showModal.value = true;
}

function openEditModal(acc) {
  selectedAccount.value = { ...acc };
  showModal.value = true;
}

async function saveAccount(formData) {
  try {
    saving.value = true;
    let res;
    if (selectedAccount.value?.id) {
      res = await AxiosHelper.put(AppsbdURL.route(`accounts/${selectedAccount.value.id}`), formData);
    } else {
      res = await AxiosHelper.post(AppsbdURL.route('accounts'), formData);
    }

    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Account saved successfully', 3000);
      showModal.value = false;
      await loadAccounts();
    }
  } catch (e) {
    console.error(e);
  } finally {
    saving.value = false;
  }
}

async function deleteAccount(id) {
  if (!confirm('Are you sure you want to delete this account?')) return;
  try {
    const res = await AxiosHelper.delete(AppsbdURL.route(`accounts/${id}`));
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Account deleted', 3000);
      await loadAccounts();
    }
  } catch (e) {
    console.error(e);
  }
}

onMounted(loadAccounts);
</script>

<style scoped lang="scss">
.account-item-card {
  background-color: var(--ab-card-bg, #ffffff);
  border: 1px solid var(--ab-border-color, #e5e7eb) !important;
  transition: transform 0.2s ease, box-shadow 0.2s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
  }
}

.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
