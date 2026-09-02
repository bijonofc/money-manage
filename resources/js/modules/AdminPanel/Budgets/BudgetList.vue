<template>
  <div class="budgets-page pb-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
          <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
            <PieChart :size="24" class="text-primary" />
            Budgets
          </h4>
          <p class="text-muted small mb-0">Set spending limits by category and stay within your financial targets</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2" @click="loadBudgets">
            <RefreshCw :size="15" :class="{ 'spin-anim': loading }" />
            <span>Refresh</span>
          </button>
          <button class="btn btn-primary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-sm" @click="openCreateModal">
            <Plus :size="16" />
            <span>New Budget</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Budgets List / Grid -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else-if="budgets.length === 0" class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
      <PieChart :size="48" class="mx-auto mb-3 opacity-50" />
      <h5>No budgets created yet</h5>
      <p class="small mb-4">Create your first budget to set spending limits and receive overbudget alerts.</p>
      <div>
        <button class="btn btn-primary rounded-pill px-4 py-2" @click="openCreateModal">
          Create Budget
        </button>
      </div>
    </div>

    <div v-else class="row g-3">
      <div v-for="b in budgets" :key="b.id" class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
              <h6 class="fw-bold mb-0 text-dark">{{ b.category?.name || 'Overall Budget' }}</h6>
              <span class="badge bg-secondary-subtle text-secondary small text-capitalize">{{ b.period }}</span>
            </div>
            <button class="btn btn-icon btn-light btn-sm rounded-circle text-danger" @click="deleteBudget(b.id)">
              <Trash2 :size="14" />
            </button>
          </div>

          <div class="mb-3">
            <span class="text-muted small">Budget Amount</span>
            <h4 class="fw-bold text-dark mb-0">{{ currencySymbol }}{{ formatNumber(b.amount) }}</h4>
          </div>

          <div class="small text-muted mb-2">
            Alert threshold: <strong>{{ b.alert_threshold }}%</strong>
          </div>
          <div class="progress rounded-pill" style="height: 8px;">
            <div class="progress-bar bg-primary rounded-pill" style="width: 45%;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Modal -->
    <div v-if="showModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
      <div class="modal-card bg-white rounded-4 shadow-lg p-4" style="max-width: 480px; width: 100%;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h5 class="fw-bold mb-0">Create Budget</h5>
          <button type="button" class="btn-close" @click="showModal = false"></button>
        </div>

        <form @submit.prevent="saveBudget">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Category</label>
            <select v-model="form.category_id" class="form-select">
              <option :value="null">All Categories (Overall)</option>
              <option v-for="cat in categoryList" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Amount *</label>
              <input v-model.number="form.amount" type="number" step="0.01" min="0.01" class="form-control" required />
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Period *</label>
              <select v-model="form.period" class="form-select" required>
                <option value="monthly">Monthly</option>
                <option value="weekly">Weekly</option>
                <option value="yearly">Yearly</option>
                <option value="daily">Daily</option>
              </select>
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Start Date *</label>
              <input v-model="form.start_date" type="date" class="form-control" required />
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Alert Threshold (%)</label>
              <input v-model.number="form.alert_threshold" type="number" min="1" max="100" class="form-control" placeholder="80" />
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4" @click="showModal = false">Cancel</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4" :disabled="saving">
              {{ saving ? 'Saving...' : 'Save Budget' }}
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
  PieChart,
  Plus,
  Trash2,
  RefreshCw,
} from '@lucide/vue';

const budgets = ref([]);
const categoryList = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);

const currencySymbol = computed(() => window.app_settings?.currencySymbol || '৳');

const form = ref({
  category_id: null,
  amount: '',
  period: 'monthly',
  start_date: new Date().toISOString().split('T')[0],
  alert_threshold: 80,
});

function formatNumber(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

async function loadBudgets() {
  try {
    loading.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('budgets/list'), {});
    if (res?.data?.rowdata) {
      budgets.value = res.data.rowdata;
    }
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

async function loadCategories() {
  try {
    const res = await AxiosHelper.post(AppsbdURL.route('categories/list'), {});
    if (res?.data?.rowdata) {
      categoryList.value = res.data.rowdata;
    }
  } catch (e) {
    console.error(e);
  }
}

function openCreateModal() {
  form.value = {
    category_id: null,
    amount: '',
    period: 'monthly',
    start_date: new Date().toISOString().split('T')[0],
    alert_threshold: 80,
  };
  showModal.value = true;
}

async function saveBudget() {
  try {
    saving.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('budgets'), form.value);
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Budget created', 3000);
      showModal.value = false;
      await loadBudgets();
    }
  } catch (e) {
    console.error(e);
  } finally {
    saving.value = false;
  }
}

async function deleteBudget(id) {
  if (!confirm('Are you sure you want to delete this budget?')) return;
  try {
    const res = await AxiosHelper.delete(AppsbdURL.route(`budgets/${id}`));
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Budget deleted', 3000);
      await loadBudgets();
    }
  } catch (e) {
    console.error(e);
  }
}

onMounted(async () => {
  await Promise.all([loadCategories(), loadBudgets()]);
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
</style>
