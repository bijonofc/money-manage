<template>
  <div class="transactions-page pb-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
          <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
            <ArrowLeftRight :size="24" class="text-primary" />
            Transactions
          </h4>
          <p class="text-muted small mb-0">Record and track income, expenses, and account transfers</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2" @click="loadTransactions">
            <RefreshCw :size="15" :class="{ 'spin-anim': loading }" />
            <span>Refresh</span>
          </button>
          <button class="btn btn-primary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-sm" @click="openCreateModal">
            <Plus :size="16" />
            <span>Add Transaction</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-3 p-3">
      <div class="row g-2 align-items-center">
        <div class="col-12 col-md-3">
          <select v-model="filterType" class="form-select form-select-sm" @change="loadTransactions">
            <option value="">All Types</option>
            <option value="income">Income</option>
            <option value="expense">Expense</option>
            <option value="transfer">Transfer</option>
          </select>
        </div>
        <div class="col-12 col-md-3">
          <select v-model="filterAccount" class="form-select form-select-sm" @change="loadTransactions">
            <option value="">All Accounts</option>
            <option v-for="acc in accountList" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Transactions Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <div v-else-if="transactions.length === 0" class="p-5 text-center text-muted">
        <ArrowLeftRight :size="48" class="mx-auto mb-3 opacity-50" />
        <h5>No transactions found</h5>
        <p class="small mb-4">Record your first transaction to keep your finances accurate.</p>
        <button class="btn btn-primary rounded-pill px-4 py-2" @click="openCreateModal">
          Add Transaction
        </button>
      </div>

      <div v-else class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-4">Description / Category</th>
              <th>Type</th>
              <th>Account</th>
              <th>Date</th>
              <th class="text-end">Amount</th>
              <th class="text-end pe-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="tx in transactions" :key="tx.id">
              <td class="ps-4">
                <div class="d-flex align-items-center gap-3">
                  <div
                    class="tx-icon-pill rounded-circle p-2 d-flex align-items-center justify-content-center"
                    :class="{
                      'bg-success-subtle text-success': tx.transaction_type === 'income',
                      'bg-danger-subtle text-danger': tx.transaction_type === 'expense',
                      'bg-primary-subtle text-primary': tx.transaction_type === 'transfer'
                    }"
                  >
                    <ArrowDownLeft v-if="tx.transaction_type === 'income'" :size="16" />
                    <ArrowUpRight v-else-if="tx.transaction_type === 'expense'" :size="16" />
                    <ArrowLeftRight v-else :size="16" />
                  </div>
                  <div>
                    <div class="fw-semibold text-dark">{{ tx.description || tx.category?.name || 'Transaction' }}</div>
                    <small class="text-muted">{{ tx.category?.name || 'Uncategorized' }}</small>
                  </div>
                </div>
              </td>
              <td>
                <span
                  class="badge text-capitalize"
                  :class="{
                    'bg-success-subtle text-success': tx.transaction_type === 'income',
                    'bg-danger-subtle text-danger': tx.transaction_type === 'expense',
                    'bg-primary-subtle text-primary': tx.transaction_type === 'transfer'
                  }"
                >
                  {{ tx.transaction_type }}
                </span>
              </td>
              <td>
                <span class="badge bg-light text-dark border">{{ tx.account?.name || 'Account' }}</span>
              </td>
              <td>
                <span class="small text-secondary">{{ tx.date }}</span>
              </td>
              <td class="text-end">
                <span
                  class="fw-bold"
                  :class="{
                    'text-success': tx.transaction_type === 'income',
                    'text-danger': tx.transaction_type === 'expense',
                    'text-primary': tx.transaction_type === 'transfer'
                  }"
                >
                  {{ tx.transaction_type === 'income' ? '+' : tx.transaction_type === 'expense' ? '-' : '' }}{{ currencySymbol }}{{ formatNumber(tx.amount) }}
                </span>
              </td>
              <td class="text-end pe-4">
                <button class="btn btn-icon btn-light btn-sm rounded-circle text-danger" @click="deleteTransaction(tx.id)">
                  <Trash2 :size="14" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Modal -->
    <div v-if="showModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
      <div class="modal-card bg-white rounded-4 shadow-lg p-4" style="max-width: 500px; width: 100%;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h5 class="fw-bold mb-0">Record Transaction</h5>
          <button type="button" class="btn-close" @click="showModal = false"></button>
        </div>

        <form @submit.prevent="saveTransaction">
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Type *</label>
              <select v-model="form.transaction_type" class="form-select" required>
                <option value="expense">Expense</option>
                <option value="income">Income</option>
                <option value="transfer">Transfer</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Amount *</label>
              <input v-model.number="form.amount" type="number" step="0.01" min="0.01" class="form-control" placeholder="0.00" required />
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Account *</label>
              <select v-model="form.account_id" class="form-select" required>
                <option v-for="acc in accountList" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Category</label>
              <select v-model="form.category_id" class="form-select">
                <option :value="null">None</option>
                <option v-for="cat in categoryList" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Date *</label>
            <input v-model="form.date" type="date" class="form-control" required />
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Description / Notes</label>
            <input v-model="form.description" type="text" class="form-control" placeholder="e.g. Grocery shopping" />
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4" @click="showModal = false">Cancel</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4" :disabled="saving">
              {{ saving ? 'Saving...' : 'Save Transaction' }}
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
  ArrowLeftRight,
  ArrowUpRight,
  ArrowDownLeft,
  Plus,
  Trash2,
  RefreshCw,
} from '@lucide/vue';

const transactions = ref([]);
const accountList = ref([]);
const categoryList = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);

const filterType = ref('');
const filterAccount = ref('');

const currencySymbol = computed(() => window.app_settings?.currencySymbol || '৳');

const form = ref({
  transaction_type: 'expense',
  amount: '',
  account_id: '',
  category_id: null,
  date: new Date().toISOString().split('T')[0],
  description: '',
});

function formatNumber(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

async function loadTransactions() {
  try {
    loading.value = true;
    const payload = {};
    if (filterType.value) payload.transaction_type = filterType.value;
    if (filterAccount.value) payload.account_id = filterAccount.value;

    const res = await AxiosHelper.post(AppsbdURL.route('transactions/list'), payload);
    if (res?.data?.rowdata) {
      transactions.value = res.data.rowdata;
    }
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

async function loadDependencies() {
  try {
    const accRes = await AxiosHelper.post(AppsbdURL.route('accounts/list'), {});
    if (accRes?.data?.rowdata) {
      accountList.value = accRes.data.rowdata;
      if (accountList.value.length > 0) form.value.account_id = accountList.value[0].id;
    }

    const catRes = await AxiosHelper.post(AppsbdURL.route('categories/list'), {});
    if (catRes?.data?.rowdata) {
      categoryList.value = catRes.data.rowdata;
    }
  } catch (e) {
    console.error(e);
  }
}

function openCreateModal() {
  form.value = {
    transaction_type: 'expense',
    amount: '',
    account_id: accountList.value[0]?.id || '',
    category_id: null,
    date: new Date().toISOString().split('T')[0],
    description: '',
  };
  showModal.value = true;
}

async function saveTransaction() {
  try {
    saving.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('transactions'), form.value);
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Transaction recorded', 3000);
      showModal.value = false;
      await loadTransactions();
    }
  } catch (e) {
    console.error(e);
  } finally {
    saving.value = false;
  }
}

async function deleteTransaction(id) {
  if (!confirm('Are you sure you want to delete this transaction?')) return;
  try {
    const res = await AxiosHelper.delete(AppsbdURL.route(`transactions/${id}`));
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Transaction deleted', 3000);
      await loadTransactions();
    }
  } catch (e) {
    console.error(e);
  }
}

onMounted(async () => {
  await Promise.all([loadDependencies(), loadTransactions()]);
});
</script>

<style scoped lang="scss">
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
