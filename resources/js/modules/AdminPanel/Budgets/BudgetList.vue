<template>
  <div class="budgets-page pb-5">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
          <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
            <PieChart :size="24" class="text-primary" />
            Budgets
          </h4>
          <p class="text-muted small mb-0">Set spending limits by category and track actual expenses in real time</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <ab-button color="secondary" is-outline :is-animated="loading" :disabled="loading" @click="loadBudgets">
            <RefreshCw :size="14" class="me-1" :class="{ 'spin-anim': loading }" />
            Refresh
          </ab-button>
          <ab-button color="primary" @click="openCreateModal">
            <Plus :size="15" class="me-1" />
            New Budget
          </ab-button>
        </div>
      </div>
    </div>

    <!-- Top KPI Summary Cards -->
    <div v-if="!loading && budgets.length > 0" class="row g-3 mb-4">
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="text-muted text-xs fw-semibold text-uppercase tracking-wider">Total Budgeted</span>
            <div class="kpi-icon-pill bg-primary-subtle text-primary">
              <Wallet :size="16" />
            </div>
          </div>
          <h4 class="fw-bold text-dark mb-0">{{ currencySymbol }}{{ formatNumber(totalBudgeted) }}</h4>
          <div class="text-muted text-xs mt-1">{{ budgets.length }} active budget{{ budgets.length > 1 ? 's' : '' }}</div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="text-muted text-xs fw-semibold text-uppercase tracking-wider">Total Spent</span>
            <div class="kpi-icon-pill bg-danger-subtle text-danger">
              <TrendingDown :size="16" />
            </div>
          </div>
          <h4 class="fw-bold text-dark mb-0">{{ currencySymbol }}{{ formatNumber(totalSpent) }}</h4>
          <div class="text-xs mt-1" :class="overallSpentPercent >= 80 ? 'text-danger fw-semibold' : 'text-muted'">
            {{ overallSpentPercent }}% utilized
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="text-muted text-xs fw-semibold text-uppercase tracking-wider">Total Remaining</span>
            <div class="kpi-icon-pill bg-success-subtle text-success">
              <CheckCircle2 :size="16" />
            </div>
          </div>
          <h4 class="fw-bold text-success mb-0">{{ currencySymbol }}{{ formatNumber(totalRemaining) }}</h4>
          <div class="text-muted text-xs mt-1">Available across budgets</div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="text-muted text-xs fw-semibold text-uppercase tracking-wider">Status</span>
            <div class="kpi-icon-pill" :class="exceededCount > 0 ? 'bg-danger-subtle text-danger' : alertCount > 0 ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success'">
              <AlertTriangle v-if="exceededCount > 0 || alertCount > 0" :size="16" />
              <Check v-else :size="16" />
            </div>
          </div>
          <h5 class="fw-bold mb-0" :class="exceededCount > 0 ? 'text-danger' : alertCount > 0 ? 'text-warning' : 'text-success'">
            {{ exceededCount > 0 ? `${exceededCount} Over Limit` : alertCount > 0 ? `${alertCount} Near Limit` : 'All On Track' }}
          </h5>
          <div class="text-muted text-xs mt-1">
            {{ exceededCount === 0 && alertCount === 0 ? 'Spending within targets' : 'Review high usage budgets' }}
          </div>
        </div>
      </div>
    </div>

    <!-- Filters & Period Bar -->
    <div v-if="!loading && budgets.length > 0" class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
      <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-2">
          <span class="text-muted text-xs fw-semibold">Period:</span>
          <div class="btn-group btn-group-sm rounded-3 bg-light p-1 border">
            <button
              v-for="p in [{ id: 'all', label: 'All' }, { id: 'monthly', label: 'Monthly' }, { id: 'weekly', label: 'Weekly' }, { id: 'yearly', label: 'Yearly' }]"
              :key="p.id"
              type="button"
              class="btn btn-sm rounded-2 py-1 px-3 border-0 transition-all text-xs"
              :class="periodFilter === p.id ? 'btn-white shadow-xs fw-bold text-dark' : 'text-muted'"
              @click="periodFilter = p.id"
            >
              {{ p.label }}
            </button>
          </div>
        </div>

        <div class="d-flex align-items-center gap-2">
          <span class="text-muted text-xs fw-semibold">Status:</span>
          <div class="btn-group btn-group-sm rounded-3 bg-light p-1 border">
            <button
              v-for="s in [{ id: 'all', label: 'All' }, { id: 'A', label: 'Active' }, { id: 'I', label: 'Inactive' }]"
              :key="s.id"
              type="button"
              class="btn btn-sm rounded-2 py-1 px-2.5 border-0 transition-all text-xs"
              :class="statusFilter === s.id ? 'btn-white shadow-xs fw-bold text-dark' : 'text-muted'"
              @click="statusFilter = s.id"
            >
              {{ s.label }}
            </button>
          </div>
        </div>
      </div>

      <div class="position-relative" style="max-width: 240px; width: 100%;">
        <input
          v-model="searchQuery"
          type="text"
          class="form-control form-control-sm rounded-3 ps-3"
          placeholder="Search category..."
        />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
      <p class="text-muted small mt-2">Loading budgets...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredBudgets.length === 0" class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
      <div class="py-4">
        <Wallet :size="48" class="text-muted opacity-50 mb-3" />
        <h5 class="fw-bold text-dark mb-1">No Budgets Found</h5>
        <p class="text-muted small mb-3">
          {{ searchQuery || periodFilter !== 'all' || statusFilter !== 'all' ? 'No budgets match your selected filters.' : 'You have not set any spending budgets yet.' }}
        </p>
        <button v-if="searchQuery || periodFilter !== 'all' || statusFilter !== 'all'" class="btn btn-sm btn-outline-secondary rounded-3 me-2" @click="resetFilters">
          Clear Filters
        </button>
        <ab-button color="primary" size="sm" @click="openCreateModal">
          Create Budget
        </ab-button>
      </div>
    </div>

    <!-- Clean Modular Budgets Grid -->
    <div v-else class="row g-3">
      <div v-for="b in filteredBudgets" :key="b.id" class="col-12 col-md-6 col-xl-4">
        <BudgetCard
          :budget="b"
          :categories="categoryList"
          :currency-symbol="currencySymbol"
          @view-expenses="openExpensesModal"
          @edit="openEditModal"
          @delete="deleteBudget"
        />
      </div>
    </div>

    <!-- Create / Edit Budget Modal Component -->
    <BudgetFormModal
      v-model="showModal"
      :edit-data="selectedBudget"
      :categories="categoryList"
      :currency-symbol="currencySymbol"
      :saving="saving"
      @save="saveBudget"
    />

    <!-- Expenses Breakdown Modal Component -->
    <BudgetBreakdownModal
      :show="showBreakdownModal"
      :budget="activeBudget"
      :categories="categoryList"
      :transactions="breakdownTransactions"
      :loading="loadingBreakdown"
      :currency-symbol="currencySymbol"
      @close="showBreakdownModal = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js';
import AppsbdURL from '@/libs/AppsbdURL.js';
import AppsbdUtls from '@/libs/AppsbdUtls.js';

import BudgetCard from './BudgetCard.vue';
import BudgetFormModal from './BudgetFormModal.vue';
import BudgetBreakdownModal from './BudgetBreakdownModal.vue';

import {
  PieChart,
  Plus,
  RefreshCw,
  Wallet,
  TrendingDown,
  CheckCircle2,
  AlertTriangle,
  Check,
  X,
} from '@lucide/vue';

const budgets = ref([]);
const categoryList = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const selectedBudget = ref(null);

const searchQuery = ref('');
const periodFilter = ref('all');
const statusFilter = ref('all');

// Breakdown Modal State
const showBreakdownModal = ref(false);
const activeBudget = ref(null);
const breakdownTransactions = ref([]);
const loadingBreakdown = ref(false);

const currencySymbol = computed(() => window.app_settings?.currencySymbol || '৳');

// Top summary KPIs
const totalBudgeted = computed(() => {
  return budgets.value.reduce((acc, b) => acc + (parseFloat(b.amount) || 0), 0);
});

const totalSpent = computed(() => {
  return budgets.value.reduce((acc, b) => acc + (parseFloat(b.spent_amount) || 0), 0);
});

const totalRemaining = computed(() => {
  return Math.max(0, totalBudgeted.value - totalSpent.value);
});

const overallSpentPercent = computed(() => {
  if (totalBudgeted.value <= 0) return 0;
  return Math.min(100, Math.round((totalSpent.value / totalBudgeted.value) * 100));
});

const exceededCount = computed(() => {
  return budgets.value.filter((b) => b.is_over_budget || ((parseFloat(b.spent_amount) || 0) > (parseFloat(b.amount) || 0))).length;
});

const alertCount = computed(() => {
  return budgets.value.filter((b) => {
    const isOver = b.is_over_budget || ((parseFloat(b.spent_amount) || 0) > (parseFloat(b.amount) || 0));
    return !isOver && (b.is_alert_reached || (parseFloat(b.progress_percentage) >= 80));
  }).length;
});

// Filtered Budgets
const filteredBudgets = computed(() => {
  return budgets.value.filter((b) => {
    if (periodFilter.value !== 'all' && b.period !== periodFilter.value) {
      return false;
    }
    if (statusFilter.value !== 'all') {
      const bStatus = b.status || (b.is_active === false ? 'I' : 'A');
      if (bStatus !== statusFilter.value) return false;
    }
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.trim().toLowerCase();
      const name = (b.category_name || b.category?.name || 'Overall Budget').toLowerCase();
      return name.includes(q);
    }
    return true;
  });
});

function formatNumber(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function resetFilters() {
  searchQuery.value = '';
  periodFilter.value = 'all';
  statusFilter.value = 'all';
}

async function loadBudgets() {
  try {
    loading.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('budgets/list'), {});
    if (res?.data?.rowdata) {
      budgets.value = res.data.rowdata;
    }
  } catch (e) {
    console.error('Error loading budgets:', e);
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
    console.error('Error loading categories:', e);
  }
}

function openCreateModal() {
  selectedBudget.value = null;
  showModal.value = true;
}

function openEditModal(b) {
  selectedBudget.value = b;
  showModal.value = true;
}

async function saveBudget(payload) {
  try {
    saving.value = true;
    let res;
    if (selectedBudget.value?.id) {
      res = await AxiosHelper.put(AppsbdURL.route(`budgets/${selectedBudget.value.id}`), payload);
    } else {
      res = await AxiosHelper.post(AppsbdURL.route('budgets'), payload);
    }

    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || (selectedBudget.value?.id ? 'Budget updated' : 'Budget created'), 3000);
      showModal.value = false;
      selectedBudget.value = null;
      await loadBudgets();
    }
  } catch (e) {
    console.error('Error saving budget:', e);
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
    console.error('Error deleting budget:', e);
  }
}

async function openExpensesModal(budget) {
  activeBudget.value = budget;
  showBreakdownModal.value = true;
  breakdownTransactions.value = [];
  loadingBreakdown.value = true;

  try {
    const res = await AxiosHelper.get(AppsbdURL.route(`budgets/${budget.id}/transactions`));
    const payload = res?.data?.data || res?.data;
    if (payload?.transactions) {
      breakdownTransactions.value = payload.transactions;
    } else if (Array.isArray(payload)) {
      breakdownTransactions.value = payload;
    }
  } catch (e) {
    console.error('Error loading budget transactions:', e);
  } finally {
    loadingBreakdown.value = false;
  }
}

onMounted(async () => {
  await Promise.all([loadCategories(), loadBudgets()]);
});
</script>

<style scoped lang="scss">
.kpi-icon-pill {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.text-xs {
  font-size: 0.75rem;
}

.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.modal-backdrop-custom {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  z-index: 1060;
}

.modal-card {
  animation: modalScale 0.18s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalScale {
  from {
    opacity: 0;
    transform: scale(0.96);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
