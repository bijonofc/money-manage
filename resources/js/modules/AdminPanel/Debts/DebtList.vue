<template>
  <div class="debts-page pb-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
          <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
            <CreditCard :size="24" class="text-danger" />
            Debts & Loans
          </h4>
          <p class="text-muted small mb-0">Track money you owe to others or money others owe to you</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2" @click="loadDebts">
            <RefreshCw :size="15" :class="{ 'spin-anim': loading }" />
            <span>Refresh</span>
          </button>
          <button class="btn btn-danger btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-sm text-white" @click="openCreateModal">
            <Plus :size="16" />
            <span>New Debt Record</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Debts Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-danger" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <div v-else-if="debts.length === 0" class="p-5 text-center text-muted">
        <CreditCard :size="48" class="mx-auto mb-3 opacity-50" />
        <h5>No debt or loan records found</h5>
        <p class="small mb-4">Keep track of personal loans, repayments, and outstanding balances.</p>
        <button class="btn btn-danger text-white rounded-pill px-4 py-2" @click="openCreateModal">
          Add Debt Record
        </button>
      </div>

      <div v-else class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-4">Person / Creditor</th>
              <th>Type</th>
              <th>Due Date</th>
              <th>Principal</th>
              <th>Paid</th>
              <th class="text-end">Remaining</th>
              <th class="text-end pe-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="d in debts" :key="d.id">
              <td class="ps-4">
                <div class="fw-semibold text-dark">{{ d.creditor_name }}</div>
                <small class="text-muted">{{ d.creditor_contact || d.description || 'No contact' }}</small>
              </td>
              <td>
                <span
                  class="badge text-capitalize"
                  :class="d.type === 'owed_to' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success'"
                >
                  {{ d.type === 'owed_to' ? 'I Owe (Payable)' : 'Owed to Me (Receivable)' }}
                </span>
              </td>
              <td>
                <span class="small text-secondary">{{ d.due_date || 'No deadline' }}</span>
              </td>
              <td>
                <span class="fw-semibold text-dark">{{ currencySymbol }}{{ formatNumber(d.principal_amount) }}</span>
              </td>
              <td>
                <span class="text-success">{{ currencySymbol }}{{ formatNumber(d.paid_amount) }}</span>
              </td>
              <td class="text-end">
                <span class="fw-bold text-danger">
                  {{ currencySymbol }}{{ formatNumber(Math.max(0, d.principal_amount - d.paid_amount)) }}
                </span>
              </td>
              <td class="text-end pe-4">
                <button
                  v-if="d.status !== 'paid'"
                  class="btn btn-sm btn-outline-success rounded-pill px-2 py-1 me-1"
                  style="font-size: 0.75rem;"
                  @click="openPayModal(d)"
                >
                  + Pay
                </button>
                <button class="btn btn-icon btn-light btn-sm rounded-circle text-danger" @click="deleteDebt(d.id)">
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
          <h5 class="fw-bold mb-0">Record Debt / Loan</h5>
          <button type="button" class="btn-close" @click="showModal = false"></button>
        </div>

        <form @submit.prevent="saveDebt">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Type *</label>
            <select v-model="form.type" class="form-select" required>
              <option value="owed_to">I Owe Money to Someone (Payable)</option>
              <option value="owed_from">Someone Owes Money to Me (Receivable)</option>
            </select>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Person / Organization *</label>
              <input v-model="form.creditor_name" type="text" class="form-control" placeholder="e.g. John Doe" required />
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Contact / Phone</label>
              <input v-model="form.creditor_contact" type="text" class="form-control" placeholder="+8801..." />
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Principal Amount *</label>
              <input v-model.number="form.principal_amount" type="number" step="0.01" min="0.01" class="form-control" required />
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Due Date</label>
              <input v-model="form.due_date" type="date" class="form-control" />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Notes / Purpose</label>
            <input v-model="form.description" type="text" class="form-control" placeholder="e.g. Friendly loan for travel" />
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4" @click="showModal = false">Cancel</button>
            <button type="submit" class="btn btn-danger text-white rounded-pill px-4" :disabled="saving">
              {{ saving ? 'Saving...' : 'Save Record' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Pay Modal -->
    <div v-if="showPayModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
      <div class="modal-card bg-white rounded-4 shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h5 class="fw-bold mb-0">Record Payment</h5>
          <button type="button" class="btn-close" @click="showPayModal = false"></button>
        </div>

        <form @submit.prevent="savePayment">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Payment Amount *</label>
            <input v-model.number="paymentAmount" type="number" step="0.01" min="0.01" class="form-control" required />
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4" @click="showPayModal = false">Cancel</button>
            <button type="submit" class="btn btn-success text-white rounded-pill px-4" :disabled="saving">
              Record Payment
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
  CreditCard,
  Plus,
  Trash2,
  RefreshCw,
} from '@lucide/vue';

const debts = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const showPayModal = ref(false);
const selectedDebt = ref(null);
const paymentAmount = ref('');

const currencySymbol = computed(() => window.app_settings?.currencySymbol || '৳');

const form = ref({
  type: 'owed_to',
  creditor_name: '',
  creditor_contact: '',
  principal_amount: '',
  due_date: '',
  description: '',
});

function formatNumber(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

async function loadDebts() {
  try {
    loading.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('debts/list'), {});
    if (res?.data?.rowdata) {
      debts.value = res.data.rowdata;
    }
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

function openCreateModal() {
  form.value = {
    type: 'owed_to',
    creditor_name: '',
    creditor_contact: '',
    principal_amount: '',
    due_date: '',
    description: '',
  };
  showModal.value = true;
}

function openPayModal(debt) {
  selectedDebt.value = debt;
  paymentAmount.value = '';
  showPayModal.value = true;
}

async function saveDebt() {
  try {
    saving.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('debts'), form.value);
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Debt record created', 3000);
      showModal.value = false;
      await loadDebts();
    }
  } catch (e) {
    console.error(e);
  } finally {
    saving.value = false;
  }
}

async function savePayment() {
  if (!selectedDebt.value) return;
  try {
    saving.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route(`debts/${selectedDebt.value.id}/pay`), {
      amount: paymentAmount.value,
    });
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Payment recorded', 3000);
      showPayModal.value = false;
      await loadDebts();
    }
  } catch (e) {
    console.error(e);
  } finally {
    saving.value = false;
  }
}

async function deleteDebt(id) {
  if (!confirm('Are you sure you want to delete this debt record?')) return;
  try {
    const res = await AxiosHelper.delete(AppsbdURL.route(`debts/${id}`));
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Record deleted', 3000);
      await loadDebts();
    }
  } catch (e) {
    console.error(e);
  }
}

onMounted(loadDebts);
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
