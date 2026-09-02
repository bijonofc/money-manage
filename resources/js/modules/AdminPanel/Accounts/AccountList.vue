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

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
      <div class="modal-card bg-white rounded-4 shadow-lg p-4" style="max-width: 480px; width: 100%;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h5 class="fw-bold mb-0">{{ editId ? 'Edit Account' : 'New Account' }}</h5>
          <button type="button" class="btn-close" @click="showModal = false"></button>
        </div>

        <form @submit.prevent="saveAccount">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Account Name *</label>
            <input v-model="form.name" type="text" class="form-control" placeholder="e.g. City Bank Salary" required />
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Account Type *</label>
            <select v-model="form.account_type" class="form-select" required>
              <option value="bank">Bank Account</option>
              <option value="cash">Cash in Hand</option>
              <option value="mobile">Mobile Money (bKash/Nagad/Rocket)</option>
              <option value="credit_card">Credit Card</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-8">
              <label class="form-label small fw-semibold">Starting Balance</label>
              <input v-model.number="form.balance" type="number" step="0.01" class="form-control" placeholder="0.00" />
            </div>
            <div class="col-4">
              <label class="form-label small fw-semibold">Currency</label>
              <input v-model="form.currency" type="text" class="form-control" placeholder="BDT" />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Account Number (Optional)</label>
            <input v-model="form.account_number" type="text" class="form-control" placeholder="XXXX-XXXX-XXXX" />
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4" @click="showModal = false">Cancel</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4" :disabled="saving">
              {{ saving ? 'Saving...' : 'Save Account' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js';
import AppsbdURL from '@/libs/AppsbdURL.js';
import AppsbdUtls from '@/libs/AppsbdUtls.js';

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
const editId = ref(null);

const currencySymbol = computed(() => window.app_settings?.currencySymbol || '৳');

const form = ref({
  name: '',
  account_type: 'bank',
  balance: 0,
  currency: 'BDT',
  account_number: '',
});

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
  editId.value = null;
  form.value = {
    name: '',
    account_type: 'bank',
    balance: 0,
    currency: 'BDT',
    account_number: '',
  };
  showModal.value = true;
}

function openEditModal(acc) {
  editId.value = acc.id;
  form.value = { ...acc };
  showModal.value = true;
}

async function saveAccount() {
  try {
    saving.value = true;
    let res;
    if (editId.value) {
      res = await AxiosHelper.put(AppsbdURL.route(`accounts/${editId.value}`), form.value);
    } else {
      res = await AxiosHelper.post(AppsbdURL.route('accounts'), form.value);
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

.modal-backdrop-custom {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  z-index: 1050;
  backdrop-filter: blur(2px);
}

.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
