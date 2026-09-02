<template>
  <div class="savings-page pb-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
          <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
            <Target :size="24" class="text-success" />
            Savings Goals
          </h4>
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

    <div v-else-if="goals.length === 0" class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
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
        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold mb-0 text-dark">{{ g.name }}</h6>
            <div class="d-flex align-items-center gap-1">
              <button class="btn btn-sm btn-outline-success rounded-pill px-2 py-1" style="font-size: 0.75rem;" @click="openContributeModal(g)">
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

          <div class="progress rounded-pill mb-2" style="height: 10px;">
            <div
              class="progress-bar bg-success rounded-pill"
              role="progressbar"
              :style="{ width: getProgress(g) + '%' }"
            ></div>
          </div>

          <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
            <span>{{ getProgress(g) }}% Saved</span>
            <span v-if="g.deadline">Due: {{ g.deadline }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Goal Modal -->
    <div v-if="showModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
      <div class="modal-card bg-white rounded-4 shadow-lg p-4" style="max-width: 480px; width: 100%;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h5 class="fw-bold mb-0">Create Savings Goal</h5>
          <button type="button" class="btn-close" @click="showModal = false"></button>
        </div>

        <form @submit.prevent="saveGoal">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Goal Name *</label>
            <input v-model="form.name" type="text" class="form-control" placeholder="e.g. Emergency Fund" required />
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Target Amount *</label>
              <input v-model.number="form.target_amount" type="number" step="0.01" min="0.01" class="form-control" placeholder="50000" required />
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Starting Amount</label>
              <input v-model.number="form.current_amount" type="number" step="0.01" min="0" class="form-control" placeholder="0.00" />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Target Date / Deadline</label>
            <input v-model="form.deadline" type="date" class="form-control" />
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4" @click="showModal = false">Cancel</button>
            <button type="submit" class="btn btn-success text-white rounded-pill px-4" :disabled="saving">
              {{ saving ? 'Saving...' : 'Save Goal' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Deposit Contribution Modal -->
    <div v-if="showDepositModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
      <div class="modal-card bg-white rounded-4 shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h5 class="fw-bold mb-0">Add Deposit</h5>
          <button type="button" class="btn-close" @click="showDepositModal = false"></button>
        </div>

        <form @submit.prevent="saveContribution">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Contribution Amount *</label>
            <input v-model.number="depositAmount" type="number" step="0.01" min="0.01" class="form-control" placeholder="1000" required />
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4" @click="showDepositModal = false">Cancel</button>
            <button type="submit" class="btn btn-success text-white rounded-pill px-4" :disabled="saving">
              Deposit
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
} from '@lucide/vue';

const goals = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const showDepositModal = ref(false);
const selectedGoal = ref(null);
const depositAmount = ref('');

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
  showDepositModal.value = true;
}

async function saveGoal() {
  try {
    saving.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('savings-goals'), form.value);
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Goal created', 3000);
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
    });
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Contribution added', 3000);
      showDepositModal.value = false;
      await loadGoals();
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

onMounted(loadGoals);
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
