<template>
  <div class="dashboard-page pb-4">
    <app-loader msg="dashboard.loading" v-if="isShowLoader" />
    <div v-else>
      <!-- Welcome & Top Action Bar -->
      <div class="welcome-card card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-1 rounded-pill">
                Money Manage
              </span>
              <span class="text-muted small">
                {{ formattedCurrentDate }}
              </span>
            </div>
            <h4 class="fw-bold mb-1 text-dark">
              Welcome back, {{ loginStore.loggedUserData.name || 'User' }} 👋
            </h4>
            <p class="text-secondary mb-0 small">
              Here's your comprehensive financial health overview and recent activity.
            </p>
          </div>

          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 rounded-pill px-3 py-2" @click="load">
              <RefreshCw :size="15" :class="{ 'spin-anim': isShowLoader }" />
              <span>Refresh</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Financial KPI Stats Grid -->
      <div class="row g-3 mb-4">
        <!-- Total Net Balance -->
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 kpi-card bg-primary text-white">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="kpi-label opacity-75 small fw-medium">Total Balance</span>
                <div class="kpi-icon-wrap rounded-circle bg-white bg-opacity-20 p-2 d-flex align-items-center justify-content-center">
                  <Wallet :size="20" class="text-white" />
                </div>
              </div>
              <div>
                <h3 class="fw-bold mb-1 text-white">
                  {{ currencySymbol }}{{ formatNumber(stats.total_balance ?? 0) }}
                </h3>
                <span class="small opacity-75">
                  Across {{ stats.total_accounts ?? 0 }} active account(s)
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Monthly Income -->
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 kpi-card">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="kpi-label text-muted small fw-medium">This Month's Income</span>
                <div class="kpi-icon-wrap rounded-circle bg-success-subtle p-2 d-flex align-items-center justify-content-center">
                  <ArrowDownLeft :size="20" class="text-success" />
                </div>
              </div>
              <div>
                <h3 class="fw-bold mb-1 text-success">
                  +{{ currencySymbol }}{{ formatNumber(stats.monthly_income ?? 0) }}
                </h3>
                <span class="small text-muted">
                  Total Income: {{ currencySymbol }}{{ formatNumber(stats.total_income ?? 0) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Monthly Expense -->
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 kpi-card">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="kpi-label text-muted small fw-medium">This Month's Expenses</span>
                <div class="kpi-icon-wrap rounded-circle bg-danger-subtle p-2 d-flex align-items-center justify-content-center">
                  <ArrowUpRight :size="20" class="text-danger" />
                </div>
              </div>
              <div>
                <h3 class="fw-bold mb-1 text-danger">
                  -{{ currencySymbol }}{{ formatNumber(stats.monthly_expense ?? 0) }}
                </h3>
                <span class="small text-muted">
                  Total Expenses: {{ currencySymbol }}{{ formatNumber(stats.total_expense ?? 0) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Net Savings -->
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 kpi-card">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="kpi-label text-muted small fw-medium">Net Monthly Savings</span>
                <div class="kpi-icon-wrap rounded-circle bg-info-subtle p-2 d-flex align-items-center justify-content-center">
                  <PiggyBank :size="20" class="text-info" />
                </div>
              </div>
              <div>
                <h3 class="fw-bold mb-1" :class="stats.net_savings >= 0 ? 'text-primary' : 'text-danger'">
                  {{ stats.net_savings >= 0 ? '+' : '' }}{{ currencySymbol }}{{ formatNumber(stats.net_savings ?? 0) }}
                </h3>
                <span class="small text-muted">
                  {{ stats.net_savings >= 0 ? 'Positive cashflow this month' : 'Spending exceeds income' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Columns -->
      <div class="row g-4 mb-4">
        <!-- Left Column: Accounts Summary & Recent Transactions -->
        <div class="col-12 col-lg-8">
          <!-- Accounts Breakdown -->
          <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
              <div>
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                  <CreditCard :size="18" class="text-primary" />
                  My Accounts
                </h6>
                <p class="text-muted small mb-0">Active balances and wallets</p>
              </div>
              <router-link to="/accounts" class="btn btn-sm btn-link text-decoration-none fw-semibold">
                Manage Accounts →
              </router-link>
            </div>
            <div class="card-body p-4">
              <div v-if="accounts.length === 0" class="text-center py-4 text-muted">
                <Wallet :size="32" class="mb-2 opacity-50" />
                <p class="mb-0">No accounts found. Create your first account to get started.</p>
              </div>
              <div v-else class="row g-3">
                <div v-for="acc in accounts" :key="acc.id" class="col-12 col-sm-6">
                  <div class="account-card p-3 rounded-3 border d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                      <div class="acc-icon-box rounded-3 p-2 bg-light d-flex align-items-center justify-content-center">
                        <CreditCard v-if="acc.account_type === 'credit_card'" :size="20" class="text-warning" />
                        <Wallet v-else-if="acc.account_type === 'cash'" :size="20" class="text-success" />
                        <Layers v-else :size="20" class="text-primary" />
                      </div>
                      <div>
                        <div class="fw-semibold text-dark text-truncate" style="max-width: 150px;">{{ acc.name }}</div>
                        <span class="badge bg-secondary-subtle text-secondary small text-capitalize">{{ acc.account_type.replace('_', ' ') }}</span>
                      </div>
                    </div>
                    <div class="text-end">
                      <div class="fw-bold text-dark">{{ currencySymbol }}{{ formatNumber(acc.balance) }}</div>
                      <small class="text-muted">{{ acc.currency || 'BDT' }}</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Transactions -->
          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
              <div>
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                  <ArrowLeftRight :size="18" class="text-primary" />
                  Recent Transactions
                </h6>
                <p class="text-muted small mb-0">Latest income and expense records</p>
              </div>
              <router-link to="/transactions" class="btn btn-sm btn-link text-decoration-none fw-semibold">
                View All →
              </router-link>
            </div>
            <div class="card-body p-4">
              <div v-if="recentTransactions.length === 0" class="text-center py-4 text-muted">
                <ArrowLeftRight :size="32" class="mb-2 opacity-50" />
                <p class="mb-0">No transactions recorded yet.</p>
              </div>
              <div v-else class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="border-0 small fw-semibold text-muted">Details</th>
                      <th class="border-0 small fw-semibold text-muted">Account</th>
                      <th class="border-0 small fw-semibold text-muted">Date</th>
                      <th class="border-0 small fw-semibold text-muted text-end">Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="tx in recentTransactions" :key="tx.id">
                      <td>
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
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Savings Goals & Category Spending Breakdown -->
        <div class="col-12 col-lg-4">
          <!-- Savings Goals Progress -->
          <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
              <div>
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                  <Target :size="18" class="text-success" />
                  Savings Goals
                </h6>
                <p class="text-muted small mb-0">Target tracking</p>
              </div>
            </div>
            <div class="card-body p-4">
              <div v-if="savingsGoals.length === 0" class="text-center py-4 text-muted">
                <Target :size="32" class="mb-2 opacity-50" />
                <p class="mb-0 small">No active savings goals set up.</p>
              </div>
              <div v-else class="d-flex flex-column gap-3">
                <div v-for="goal in savingsGoals" :key="goal.id" class="goal-item">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="fw-semibold text-dark small">{{ goal.name }}</span>
                    <span class="small fw-bold text-success">{{ getProgress(goal) }}%</span>
                  </div>
                  <div class="progress rounded-pill mb-1" style="height: 8px;">
                    <div
                      class="progress-bar bg-success rounded-pill"
                      role="progressbar"
                      :style="{ width: getProgress(goal) + '%' }"
                    ></div>
                  </div>
                  <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                    <span>{{ currencySymbol }}{{ formatNumber(goal.current_amount) }}</span>
                    <span>Target: {{ currencySymbol }}{{ formatNumber(goal.target_amount) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Top Spending by Category -->
          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
              <div>
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                  <PieChart :size="18" class="text-danger" />
                  Monthly Spending
                </h6>
                <p class="text-muted small mb-0">By Category</p>
              </div>
            </div>
            <div class="card-body p-4">
              <div v-if="categorySpending.length === 0" class="text-center py-4 text-muted">
                <PieChart :size="32" class="mb-2 opacity-50" />
                <p class="mb-0 small">No expense data recorded this month.</p>
              </div>
              <div v-else class="d-flex flex-column gap-3">
                <div v-for="cat in categorySpending" :key="cat.name" class="cat-item">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="d-flex align-items-center gap-2">
                      <span class="dot-indicator" :style="{ backgroundColor: cat.color || '#ef4444' }"></span>
                      <span class="fw-medium text-dark small">{{ cat.name }}</span>
                    </div>
                    <span class="fw-semibold text-dark small">{{ currencySymbol }}{{ formatNumber(cat.total_amount) }}</span>
                  </div>
                  <div class="progress rounded-pill" style="height: 6px;">
                    <div
                      class="progress-bar rounded-pill"
                      :style="{
                        width: getCatPercent(cat.total_amount) + '%',
                        backgroundColor: cat.color || '#ef4444'
                      }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { AppLoader } from "@appsbd/vue3-appsbd-libs";
import { useDashboardStore } from "@/modules/AdminPanel/Dashboard/DashboardStore.js";
import { useLoginStore } from "@/modules/AdminPanel/User/loginStore.js";

// Lucide Icons
import {
  Wallet,
  ArrowUpRight,
  ArrowDownLeft,
  ArrowLeftRight,
  PiggyBank,
  CreditCard,
  Target,
  PieChart,
  Layers,
  RefreshCw,
} from '@lucide/vue';

const dashboardStore = useDashboardStore();
const loginStore = useLoginStore();
const isShowLoader = ref(false);

const currencySymbol = computed(() => window.app_settings?.currencySymbol || '৳');

const d = computed(() => dashboardStore.initialData ?? {});
const stats = computed(() => d.value.stats ?? {});
const accounts = computed(() => d.value.accounts ?? []);
const recentTransactions = computed(() => d.value.recent_transactions ?? []);
const savingsGoals = computed(() => d.value.savings_goals ?? []);
const categorySpending = computed(() => d.value.category_spending ?? []);

const formattedCurrentDate = computed(() => {
  return new Date().toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
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

function getCatPercent(catAmount) {
  const total = stats.value.monthly_expense || 1;
  return Math.min(Math.round((parseFloat(catAmount) / total) * 100), 100);
}

async function load() {
  if (!loginStore.isLoggedIn) return;
  try {
    isShowLoader.value = true;
    await dashboardStore.getInitialData();
  } catch (e) {
    console.error(e);
  } finally {
    isShowLoader.value = false;
  }
}

onMounted(load);
</script>

<style scoped lang="scss">
.welcome-card {
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.06) 0%, rgba(244, 246, 250, 0.5) 100%);
  border: 1px solid rgba(99, 102, 241, 0.12) !important;
}

.kpi-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  &:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
  }
}

.account-card {
  background-color: var(--ab-card-bg, #ffffff);
  transition: all 0.2s ease;
  &:hover {
    border-color: #6366f1 !important;
    background-color: rgba(99, 102, 241, 0.02);
  }
}

.dot-indicator {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
}

.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
