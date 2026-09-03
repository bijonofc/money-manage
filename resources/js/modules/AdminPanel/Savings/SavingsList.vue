<template>
  <div class="savings-page pb-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
      <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
          <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 text-xs fw-bold">
              Goals & Targets
            </span>
            <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
              <Target :size="24" class="text-success" />
              Savings Goals
            </h4>
          </div>
          <p class="text-muted small mb-0">Track your progress towards emergencies, vacations, and future investments</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2" @click="loadGoals">
            <RefreshCw :size="15" :class="{ 'spin-anim': loading }" />
            <span>Refresh</span>
          </button>
          <button class="btn btn-success btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-sm text-white" @click="openCreateModal">
            <Plus :size="16" />
            <span>New Goal</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Goals Grid -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-success" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else-if="goals.length === 0" class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted bg-white">
      <Target :size="48" class="mx-auto mb-3 opacity-50" />
      <h5>No savings goals created yet</h5>
      <p class="small mb-4">Create your first goal to save towards your dreams.</p>
      <div>
        <button class="btn btn-success text-white rounded-pill px-4 py-2" @click="openCreateModal">
          Create Goal
        </button>
      </div>
    </div>

    <div v-else class="row g-3">
      <div v-for="g in goals" :key="g.id" class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold mb-0 text-dark">{{ g.name }}</h6>
            <div class="d-flex align-items-center gap-1">
              <button class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 text-xs fw-semibold" @click="openContributeModal(g)">
                + Deposit
              </button>
              <button class="btn btn-icon btn-light btn-sm rounded-circle text-danger" @click="deleteGoal(g.id)">
                <Trash2 :size="14" />
              </button>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-baseline mb-2">
            <h4 class="fw-bold text-success mb-0">{{ currencySymbol }}{{ formatNumber(g.current_amount) }}</h4>
            <span class="text-muted small">Target: {{ currencySymbol }}{{ formatNumber(g.target_amount) }}</span>
          </div>

          <div class="progress rounded-pill mb-2" style="height: 8px;">
            <div
              class="progress-bar bg-success rounded-pill"
              role="progressbar"
              :style="{ width: getProgress(g) + '%' }"
            ></div>
          </div>

          <div class="d-flex justify-content-between text-muted text-xxs">
            <span class="fw-semibold text-dark">{{ getProgress(g) }}% Saved</span>
            <span v-if="g.deadline">📅 Due: {{ g.deadline }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Goal Modal -->
    <div v-if="showModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
      <div class="modal-card bg-white rounded-4 shadow-lg p-4" style="max-width: 480px; width: 100%;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <Target :size="18" class="text-success" />
            Create Savings Goal
          </h5>
          <button type="button" class="btn-close" @click="showModal = false"></button>
        </div>

        <form @submit.prevent="saveGoal">
          <div class="mb-3">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Goal Name *</label>
            <input v-model="form.name" type="text" class="form-control text-xs" placeholder="e.g. Emergency Fund, New Laptop" required />
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Target Amount (৳) *</label>
              <input v-model.number="form.target_amount" type="number" step="0.01" min="0.01" class="form-control text-xs fw-bold" placeholder="50000" required />
            </div>
            <div class="col-6">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Starting Amount (৳)</label>
              <input v-model.number="form.current_amount" type="number" step="0.01" min="0" class="form-control text-xs" placeholder="0" />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Target Deadline (Optional)</label>
            <input v-model="form.deadline" type="date" class="form-control text-xs" />
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4 text-xs fw-semibold" @click="showModal = false">Cancel</button>
            <button type="submit" class="btn btn-success text-white rounded-pill px-4 text-xs fw-semibold" :disabled="saving">
              {{ saving ? 'Saving...' : 'Create Goal' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Deposit Contribution Modal -->
    <div v-if="showDepositModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
      <div class="modal-card bg-white rounded-4 shadow-lg p-4" style="max-width: 440px; width: 100%;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <Target :size="18" class="text-success" />
            Deposit to {{ selectedGoal?.name }}
          </h5>
          <button type="button" class="btn-close" @click="showDepositModal = false"></button>
        </div>

        <div v-if="selectedGoal" class="alert alert-light border p-2.5 rounded-3 mb-3 text-xs">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Target Amount:</span>
            <span class="fw-bold text-dark">৳{{ formatNumber(selectedGoal.target_amount) }}</span>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Already Saved:</span>
            <span class="fw-bold text-success">৳{{ formatNumber(selectedGoal.current_amount) }}</span>
          </div>
        </div>

        <form @submit.prevent="saveContribution">
          <div class="mb-3">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Deposit Amount (৳) *</label>
            <input v-model.number="depositAmount" type="number" step="0.01" min="0.01" class="form-control text-xs fw-bold" placeholder="1000" required />
          </div>

          <!-- Account to Deduct From -->
          <div class="mb-3">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Pay From Account *</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted">
                <Wallet :size="14" />
              </span>
              <select v-model="depositAccountId" class="form-select border-start-0 text-xs" required>
                <option value="" disabled>Select account...</option>
                <option v-for="acc in accountList" :key="acc.id" :value="acc.id">
                  {{ acc.name }} (৳{{ formatNumber(acc.balance) }})
                </option>
              </select>
            </div>
            <small class="text-xxs text-muted mt-1 d-block">
              ✨ Will deduct from this account and record a savings transaction.
            </small>
          </div>

          <div class="mb-3">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Note (Optional)</label>
            <input v-model="depositNote" type="text" class="form-control text-xs" placeholder="e.g. Monthly salary savings" />
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4 text-xs fw-semibold" @click="showDepositModal = false">Cancel</button>
            <button type="submit" class="btn btn-success text-white rounded-pill px-4 text-xs fw-semibold" :disabled="saving">
              {{ saving ? 'Saving...' : 'Record Deposit' }}
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
  Target,
  Plus,
  Trash2,
  RefreshCw,
  Wallet,
} from '@lucide/vue';

const goals = ref([]);
const accountList = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const showDepositModal = ref(false);
const selectedGoal = ref(null);

const depositAmount = ref('');
const depositAccountId = ref('');
const depositNote = ref('');

const currencySymbol = computed(() => window.app_settings?.currencySymbol || '৳');

const form = ref({
  name: '',
  target_amount: '',
  current_amount: 0,
  deadline: '',
});

function formatNumber(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function getProgress(goal) {
  if (!goal.target_amount || goal.target_amount <= 0) return 0;
  const pct = (parseFloat(goal.current_amount || 0) / parseFloat(goal.target_amount)) * 100;
  return Math.min(Math.round(pct), 100);
}

async function loadAccounts() {
  try {
    const res = await AxiosHelper.post(AppsbdURL.route('accounts/list'), {});
    if (res?.data?.rowdata) {
      accountList.value = res.data.rowdata;
    } else if (Array.isArray(res?.data)) {
      accountList.value = res.data;
    }
  } catch (e) {
    console.error('Failed to load accounts', e);
  }
}

async function loadGoals() {
  try {
    loading.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('savings-goals/list'), {});
    if (res?.data?.rowdata) {
      goals.value = res.data.rowdata;
    }
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

function openCreateModal() {
  form.value = {
    name: '',
    target_amount: '',
    current_amount: 0,
    deadline: '',
  };
  showModal.value = true;
}

function openContributeModal(g) {
  selectedGoal.value = g;
  depositAmount.value = '';
  depositAccountId.value = accountList.value[0]?.id || '';
  depositNote.value = '';
  showDepositModal.value = true;
}

async function saveGoal() {
  try {
    saving.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('savings-goals'), form.value);
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Goal created successfully', 3000);
      showModal.value = false;
      await loadGoals();
    }
  } catch (e) {
    console.error(e);
  } finally {
    saving.value = false;
  }
}

async function saveContribution() {
  if (!selectedGoal.value) return;
  try {
    saving.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route(`savings-goals/${selectedGoal.value.id}/contribute`), {
      amount: depositAmount.value,
      account_id: depositAccountId.value,
      note: depositNote.value,
    });
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Contribution added and account balance updated', 3000);
      showDepositModal.value = false;
      await Promise.all([loadGoals(), loadAccounts()]);
    }
  } catch (e) {
    console.error(e);
  } finally {
    saving.value = false;
  }
}

async function deleteGoal(id) {
  if (!confirm('Are you sure you want to delete this savings goal?')) return;
  try {
    const res = await AxiosHelper.delete(AppsbdURL.route(`savings-goals/${id}`));
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Goal deleted', 3000);
      await loadGoals();
    }
  } catch (e) {
    console.error(e);
  }
}

onMounted(async () => {
  await Promise.all([loadAccounts(), loadGoals()]);
});
</script>

<style scoped lang="scss">
.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.text-xxs {
  font-size: 0.6875rem;
}

.tracking-wider {
  letter-spacing: 0.05em;
}
</style>
