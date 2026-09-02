<template>
  <div class="reports-page pb-5">
    <!-- Top Header & Filter Controls -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
      <div class="card-body p-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 text-xs fw-bold">
                Analytics
              </span>
              <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <BarChart3 :size="24" class="text-primary" />
                Financial Reports
              </h4>
            </div>
            <p class="text-muted small mb-0">
              Track cash flow, category breakdowns, savings rate, and financial trends
            </p>
          </div>

          <!-- Actions: Export CSV / Print -->
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <button
              type="button"
              class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-1.5 shadow-sm"
              @click="loadReportData"
            >
              <RefreshCw :size="14" :class="{ 'spin-anim': loading }" />
              <span>Refresh</span>
            </button>

            <button
              type="button"
              class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-1.5 shadow-sm"
              :disabled="exporting"
              @click="exportCsv"
            >
              <Download :size="14" />
              <span>{{ exporting ? 'Exporting...' : 'Export CSV' }}</span>
            </button>

            <button
              type="button"
              class="btn btn-light btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-1.5 shadow-sm text-secondary"
              @click="printReport"
            >
              <Printer :size="14" />
              <span>Print</span>
            </button>
          </div>
        </div>

        <hr class="my-3 opacity-10" />

        <!-- FILTERS: Preset Pills + Account Selector + Custom Date Range -->
        <div class="row g-3 align-items-center">
          <!-- Preset Pills -->
          <div class="col-12 col-xl-7">
            <div class="d-flex align-items-center gap-1.5 flex-wrap">
              <span class="text-xs text-muted fw-bold me-1 text-uppercase tracking-wider">Period:</span>
              <button
                v-for="p in presets"
                :key="p.id"
                type="button"
                class="btn btn-sm rounded-pill px-3 py-1 text-xs fw-semibold transition-all"
                :class="selectedPreset === p.id ? 'btn-primary text-white shadow-sm' : 'btn-light text-secondary'"
                @click="selectPreset(p.id)"
              >
                {{ p.label }}
              </button>
            </div>
          </div>

          <!-- Account Filter -->
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="input-group input-group-sm rounded-pill overflow-hidden border">
              <span class="input-group-text bg-light border-0 text-muted ps-3">
                <Wallet :size="14" />
              </span>
              <select
                v-model="selectedAccountId"
                class="form-select form-select-sm border-0 bg-light text-xs ps-1"
                @change="loadReportData"
              >
                <option value="">All Accounts</option>
                <option v-for="acc in accountList" :key="acc.id" :value="acc.id">
                  {{ acc.name }}
                </option>
              </select>
            </div>
          </div>

          <!-- Custom Date Toggle / Info Badge -->
          <div class="col-12 col-sm-6 col-xl-2 text-xl-end">
            <span class="badge bg-light text-dark border px-3 py-2 text-xxs fw-medium rounded-pill">
              📅 {{ formattedDateRange }}
            </span>
          </div>
        </div>

        <!-- Custom Date Range Row (Shown when 'custom' is active) -->
        <div v-if="selectedPreset === 'custom'" class="row g-2 mt-2 pt-2 border-top animate-fade-in">
          <div class="col-6 col-md-3">
            <label class="form-label text-xxs text-muted mb-1">Start Date</label>
            <input v-model="customStartDate" type="date" class="form-control form-control-sm text-xs" />
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label text-xxs text-muted mb-1">End Date</label>
            <input v-model="customEndDate" type="date" class="form-control form-control-sm text-xs" />
          </div>
          <div class="col-12 col-md-2 d-flex align-items-end">
            <button
              type="button"
              class="btn btn-primary btn-sm w-100 rounded-pill text-xs fw-semibold"
              @click="loadReportData"
            >
              Apply Filter
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading && !reportData" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="text-muted text-xs mt-2">Crunching financial numbers...</p>
    </div>

    <!-- Main Report Content -->
    <div v-else-if="reportData" class="report-content-container animate-fade-in">

      <!-- 1. EXECUTIVE KPI SUMMARY CARDS -->
      <div class="row g-3 mb-4">
        <!-- Total Income -->
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white h-100 kpi-card kpi-income">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="text-xs fw-bold text-uppercase tracking-wider text-muted">Total Income</span>
              <div class="p-2 rounded-3 bg-success-subtle text-success">
                <ArrowDownLeft :size="18" />
              </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark">৳ {{ formatNumber(summary.total_income) }}</h3>
            <div class="d-flex align-items-center gap-1 text-success text-xxs fw-semibold">
              <Sparkles :size="12" />
              <span>Inflow across {{ summary.transactions_count }} entries</span>
            </div>
          </div>
        </div>

        <!-- Total Expenses -->
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white h-100 kpi-card kpi-expense">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="text-xs fw-bold text-uppercase tracking-wider text-muted">Total Expenses</span>
              <div class="p-2 rounded-3 bg-danger-subtle text-danger">
                <ArrowUpRight :size="18" />
              </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark">৳ {{ formatNumber(summary.total_expense) }}</h3>
            <div class="d-flex align-items-center gap-1 text-muted text-xxs">
              <span>Avg. ৳{{ formatNumber(summary.daily_average_expense) }} / day</span>
            </div>
          </div>
        </div>

        <!-- Net Savings / Cash Flow -->
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white h-100 kpi-card kpi-savings">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="text-xs fw-bold text-uppercase tracking-wider text-muted">Net Cash Flow</span>
              <div
                class="p-2 rounded-3"
                :class="summary.net_savings >= 0 ? 'bg-primary-subtle text-primary' : 'bg-danger-subtle text-danger'"
              >
                <TrendingUp v-if="summary.net_savings >= 0" :size="18" />
                <TrendingDown v-else :size="18" />
              </div>
            </div>
            <h3
              class="fw-bold mb-1"
              :class="summary.net_savings >= 0 ? 'text-primary' : 'text-danger'"
            >
              {{ summary.net_savings >= 0 ? '+' : '-' }}৳ {{ formatNumber(Math.abs(summary.net_savings)) }}
            </h3>
            <div class="d-flex align-items-center gap-1 text-xxs fw-semibold" :class="summary.net_savings >= 0 ? 'text-primary' : 'text-danger'">
              <span>{{ summary.net_savings >= 0 ? 'Surplus retained' : 'Deficit overspend' }}</span>
            </div>
          </div>
        </div>

        <!-- Savings Rate % -->
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white h-100 kpi-card kpi-rate">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="text-xs fw-bold text-uppercase tracking-wider text-muted">Savings Rate</span>
              <div class="p-2 rounded-3 bg-info-subtle text-info">
                <Percent :size="18" />
              </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
              <h3 class="fw-bold mb-0 text-dark">{{ summary.savings_rate }}%</h3>
              <span
                class="badge rounded-pill text-xxs"
                :class="summary.savings_rate >= 30 ? 'bg-success-subtle text-success' : summary.savings_rate >= 10 ? 'bg-info-subtle text-info' : 'bg-warning-subtle text-warning'"
              >
                {{ summary.savings_rate >= 30 ? 'High' : summary.savings_rate >= 10 ? 'Moderate' : 'Low' }}
              </span>
            </div>
            <div class="progress progress-modern rounded-pill" style="height: 6px;">
              <div
                class="progress-bar bg-success rounded-pill transition-all"
                :style="{ width: Math.max(0, Math.min(100, summary.savings_rate)) + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. MONTHLY CASH FLOW TREND (Visual Bar Chart) -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
              <Layers :size="18" class="text-primary" />
              Monthly Income vs Expense Trend
            </h5>
            <small class="text-muted">6-month cashflow comparison</small>
          </div>
          <div class="d-flex align-items-center gap-3 text-xs">
            <div class="d-flex align-items-center gap-1.5">
              <span class="legend-indicator bg-success"></span>
              <span class="text-muted">Income</span>
            </div>
            <div class="d-flex align-items-center gap-1.5">
              <span class="legend-indicator bg-danger"></span>
              <span class="text-muted">Expense</span>
            </div>
          </div>
        </div>

        <!-- Custom Responsive Trend Bars -->
        <div class="trend-chart-wrapper pt-3 pb-2">
          <div class="row g-2 align-items-end text-center h-100">
            <div
              v-for="m in monthlyTrend"
              :key="m.month_key"
              class="col"
            >
              <div class="trend-column d-flex flex-column align-items-center justify-content-end h-100">
                <!-- Bar Pair Container -->
                <div class="bars-container d-flex align-items-end justify-content-center gap-1 mb-2">
                  <!-- Income Bar -->
                  <div
                    class="trend-bar bar-income rounded-top-2"
                    :style="{ height: getBarHeight(m.income) }"
                    :title="`Income: ৳${formatNumber(m.income)}`"
                  ></div>
                  <!-- Expense Bar -->
                  <div
                    class="trend-bar bar-expense rounded-top-2"
                    :style="{ height: getBarHeight(m.expense) }"
                    :title="`Expense: ৳${formatNumber(m.expense)}`"
                  ></div>
                </div>

                <!-- Label & Net -->
                <span class="fw-bold text-xs text-dark d-block">{{ m.short }}</span>
                <small
                  class="text-xxs fw-semibold d-block"
                  :class="m.savings >= 0 ? 'text-success' : 'text-danger'"
                >
                  {{ m.savings >= 0 ? '+' : '-' }}৳{{ formatCompact(Math.abs(m.savings)) }}
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. CATEGORY BREAKDOWN (EXPENSES & INCOMES) -->
      <div class="row g-4 mb-4">
        
        <!-- EXPENSE BY CATEGORY (Ranked Progress List) -->
        <div class="col-12 col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                  <PieChart :size="18" class="text-danger" />
                  Expenses by Category
                </h5>
                <small class="text-muted">Ranked spending breakdown</small>
              </div>
              <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1 text-xxs fw-bold">
                {{ expenseCategories.length }} Categories
              </span>
            </div>

            <!-- Empty State -->
            <div v-if="expenseCategories.length === 0" class="text-center py-5 text-muted">
              <Tag :size="32" class="opacity-50 mb-2" />
              <p class="text-xs mb-0">No expense records found in this period</p>
            </div>

            <!-- Category Ranked List -->
            <div v-else class="category-breakdown-list d-flex flex-column gap-3 mt-2">
              <div
                v-for="cat in expenseCategories"
                :key="cat.id"
                class="category-stat-item"
              >
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <div class="d-flex align-items-center gap-2">
                    <div
                      class="cat-color-dot rounded-circle p-1.5 d-flex align-items-center justify-content-center text-white"
                      :style="{ backgroundColor: cat.color || '#ef4444' }"
                    >
                      <Tag :size="12" />
                    </div>
                    <span class="fw-semibold text-xs text-dark">{{ cat.name }}</span>
                    <span class="badge bg-light text-muted text-xxs border">{{ cat.tx_count }} txns</span>
                  </div>
                  <div class="text-end">
                    <span class="fw-bold text-xs text-dark d-block">৳ {{ formatNumber(cat.total_amount) }}</span>
                    <small class="text-muted text-xxs">{{ cat.percentage }}%</small>
                  </div>
                </div>

                <div class="progress progress-modern rounded-pill" style="height: 6px;">
                  <div
                    class="progress-bar rounded-pill transition-all"
                    :style="{ width: cat.percentage + '%', backgroundColor: cat.color || '#ef4444' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- INCOME BY CATEGORY -->
        <div class="col-12 col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                  <Coins :size="18" class="text-success" />
                  Income by Source
                </h5>
                <small class="text-muted">Earnings stream composition</small>
              </div>
              <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 text-xxs fw-bold">
                {{ incomeCategories.length }} Sources
              </span>
            </div>

            <!-- Empty State -->
            <div v-if="incomeCategories.length === 0" class="text-center py-5 text-muted">
              <Coins :size="32" class="opacity-50 mb-2" />
              <p class="text-xs mb-0">No income records found in this period</p>
            </div>

            <!-- Income Ranked List -->
            <div v-else class="category-breakdown-list d-flex flex-column gap-3 mt-2">
              <div
                v-for="cat in incomeCategories"
                :key="cat.id"
                class="category-stat-item"
              >
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <div class="d-flex align-items-center gap-2">
                    <div
                      class="cat-color-dot rounded-circle p-1.5 d-flex align-items-center justify-content-center text-white"
                      :style="{ backgroundColor: cat.color || '#10b981' }"
                    >
                      <Tag :size="12" />
                    </div>
                    <span class="fw-semibold text-xs text-dark">{{ cat.name }}</span>
                    <span class="badge bg-light text-muted text-xxs border">{{ cat.tx_count }} txns</span>
                  </div>
                  <div class="text-end">
                    <span class="fw-bold text-xs text-dark d-block">৳ {{ formatNumber(cat.total_amount) }}</span>
                    <small class="text-muted text-xxs">{{ cat.percentage }}%</small>
                  </div>
                </div>

                <div class="progress progress-modern rounded-pill" style="height: 6px;">
                  <div
                    class="progress-bar rounded-pill transition-all"
                    :style="{ width: cat.percentage + '%', backgroundColor: cat.color || '#10b981' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- 4. ACCOUNT CASH FLOW TABLE -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
              <Building2 :size="18" class="text-primary" />
              Account Liquidity & Net Movement
            </h5>
            <small class="text-muted">Inflow, outflow, and net balance changes per account</small>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-xs text-uppercase tracking-wider text-muted">
              <tr>
                <th class="border-0 ps-3">Account</th>
                <th class="border-0">Type</th>
                <th class="border-0 text-end">Inflows</th>
                <th class="border-0 text-end">Outflows</th>
                <th class="border-0 text-end">Net Movement</th>
                <th class="border-0 text-end pe-3">Current Balance</th>
              </tr>
            </thead>
            <tbody class="text-xs">
              <tr v-for="acc in accountFlows" :key="acc.id">
                <td class="ps-3 fw-bold text-dark">{{ acc.name }}</td>
                <td>
                  <span class="badge bg-light text-secondary text-capitalize border">{{ acc.account_type.replace('_', ' ') }}</span>
                </td>
                <td class="text-end text-success fw-semibold">+৳ {{ formatNumber(acc.inflows) }}</td>
                <td class="text-end text-danger fw-semibold">-৳ {{ formatNumber(acc.outflows) }}</td>
                <td class="text-end fw-bold" :class="acc.net_flow >= 0 ? 'text-primary' : 'text-danger'">
                  {{ acc.net_flow >= 0 ? '+' : '-' }}৳ {{ formatNumber(Math.abs(acc.net_flow)) }}
                </td>
                <td class="text-end pe-3 fw-bolder text-dark">৳ {{ formatNumber(acc.current_balance) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
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
  BarChart3,
  PieChart,
  TrendingUp,
  TrendingDown,
  ArrowUpRight,
  ArrowDownLeft,
  Calendar,
  Wallet,
  Tag,
  Download,
  Printer,
  RefreshCw,
  Coins,
  Percent,
  Sparkles,
  Layers,
  Building2,
} from '@lucide/vue';

const loading = ref(false);
const exporting = ref(false);
const reportData = ref(null);

const accountList = ref([]);
const selectedAccountId = ref('');
const selectedPreset = ref('this_month');
const customStartDate = ref('');
const customEndDate = ref('');

const presets = [
  { id: 'this_month', label: 'This Month' },
  { id: 'last_month', label: 'Last Month' },
  { id: 'last_3_months', label: 'Last 3 Months' },
  { id: 'last_6_months', label: 'Last 6 Months' },
  { id: 'this_year', label: 'This Year' },
  { id: 'custom', label: 'Custom Range' },
];

const summary = computed(() => reportData.value?.summary || {
  total_income: 0,
  total_expense: 0,
  net_savings: 0,
  savings_rate: 0,
  daily_average_expense: 0,
  transactions_count: 0,
});

const expenseCategories = computed(() => reportData.value?.expense_categories || []);
const incomeCategories = computed(() => reportData.value?.income_categories || []);
const monthlyTrend = computed(() => reportData.value?.monthly_trend || []);
const accountFlows = computed(() => reportData.value?.account_flows || []);

const formattedDateRange = computed(() => {
  if (!reportData.value?.period) return 'Selected Period';
  const { start_date, end_date } = reportData.value.period;
  return `${start_date} to ${end_date}`;
});

function formatNumber(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatCompact(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0';
  if (n >= 100000) return (n / 100000).toFixed(1) + 'L';
  if (n >= 1000) return (n / 1000).toFixed(1) + 'k';
  return n.toString();
}

function getBarHeight(val) {
  if (!monthlyTrend.value || monthlyTrend.value.length === 0) return '8px';
  const maxVal = Math.max(
    ...monthlyTrend.value.map(m => Math.max(m.income, m.expense)),
    1000
  );
  const pct = Math.max(6, Math.min(100, (val / maxVal) * 100));
  return `${pct}%`;
}

function selectPreset(presetId) {
  selectedPreset.value = presetId;
  if (presetId !== 'custom') {
    loadReportData();
  }
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

async function loadReportData() {
  try {
    loading.value = true;
    const params = {
      preset: selectedPreset.value,
      account_id: selectedAccountId.value || undefined,
    };
    if (selectedPreset.value === 'custom') {
      params.start_date = customStartDate.value;
      params.end_date = customEndDate.value;
    }

    const res = await AxiosHelper.post(AppsbdURL.route('reports/overview'), params);
    if (res?.status && res?.data) {
      reportData.value = res.data;
    }
  } catch (e) {
    console.error('Failed to load report data', e);
  } finally {
    loading.value = false;
  }
}

async function exportCsv() {
  try {
    exporting.value = true;
    const params = {
      preset: selectedPreset.value,
      account_id: selectedAccountId.value || undefined,
      start_date: reportData.value?.period?.start_date,
      end_date: reportData.value?.period?.end_date,
    };

    const res = await AxiosHelper.post(AppsbdURL.route('reports/export'), params);
    if (res?.status && res?.data?.rows) {
      const rows = res.data.rows;
      if (rows.length === 0) {
        AppsbdUtls.ShowServerResponseNotification('No transactions to export', 3000);
        return;
      }

      // Convert rows to CSV string
      const headers = Object.keys(rows[0]);
      const csvContent = [
        headers.join(','),
        ...rows.map(r => headers.map(h => `"${String(r[h] || '').replace(/"/g, '""')}"`).join(','))
      ].join('\n');

      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.setAttribute('href', url);
      link.setAttribute('download', res.data.filename || 'financial_report.csv');
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      AppsbdUtls.ShowServerResponseNotification('Report exported successfully!', 3000);
    }
  } catch (e) {
    console.error('Export failed', e);
  } finally {
    exporting.value = false;
  }
}

function printReport() {
  window.print();
}

onMounted(async () => {
  await Promise.all([loadAccounts(), loadReportData()]);
});
</script>

<style scoped lang="scss">
.kpi-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border-left: 4px solid transparent !important;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
  }

  &.kpi-income { border-left-color: #10b981 !important; }
  &.kpi-expense { border-left-color: #ef4444 !important; }
  &.kpi-savings { border-left-color: #137035 !important; }
  &.kpi-rate { border-left-color: #06b6d4 !important; }
}

.trend-chart-wrapper {
  height: 220px;
}

.bars-container {
  height: 140px;
  width: 100%;
}

.trend-bar {
  width: 16px;
  min-height: 6px;
  transition: height 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  cursor: pointer;

  &.bar-income {
    background: linear-gradient(180deg, #34d399 0%, #10b981 100%);
    &:hover { opacity: 0.85; }
  }

  &.bar-expense {
    background: linear-gradient(180deg, #f87171 0%, #ef4444 100%);
    &:hover { opacity: 0.85; }
  }
}

.legend-indicator {
  width: 10px;
  height: 10px;
  border-radius: 2px;
  display: inline-block;
}

.cat-color-dot {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
}

.text-xxs {
  font-size: 0.6875rem;
}

.tracking-wider {
  letter-spacing: 0.05em;
}

.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-fade-in {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(6px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
