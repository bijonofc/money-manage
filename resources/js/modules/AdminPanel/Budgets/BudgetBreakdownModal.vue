<template>
  <div v-if="show" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3" @click.self="$emit('close')">
    <div class="modal-card bg-white rounded-4 shadow-xl border-0 overflow-hidden" style="max-width: 640px; width: 100%;">
      <!-- Modal Header -->
      <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
          <div
            class="category-icon-box rounded-3 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0"
            :style="{ backgroundColor: categoryBg, color: categoryColor }"
          >
            <component :is="categoryIcon" :size="20" />
          </div>
          <div>
            <h5 class="fw-bold mb-0 text-dark">{{ budgetName }}</h5>
            <div class="text-muted text-xs mt-0.5 d-flex align-items-center gap-1.5">
              <Calendar :size="12" class="opacity-70" />
              <span>{{ formattedDate }}</span>
              <span class="opacity-40">&bull;</span>
              <span>Expense Breakdown</span>
            </div>
          </div>
        </div>
        <button type="button" class="btn btn-icon btn-light rounded-circle btn-sm" @click="$emit('close')">
          <X :size="16" />
        </button>
      </div>

      <div class="p-4">
        <!-- Budget Metric Stats in Modal -->
        <div class="row g-3 mb-3">
          <div class="col-4">
            <div class="p-3 rounded-3 bg-light text-center border">
              <span class="text-muted text-xxs text-uppercase fw-bold tracking-wider d-block mb-1">Budget Target</span>
              <span class="fw-bold text-dark fs-6">{{ currencySymbol }}{{ formatNumber(budget?.amount) }}</span>
            </div>
          </div>
          <div class="col-4">
            <div class="p-3 rounded-3 bg-danger-subtle text-center border border-danger-subtle">
              <span class="text-danger text-xxs text-uppercase fw-bold tracking-wider d-block mb-1">Total Spent</span>
              <span class="fw-bold text-danger fs-6">{{ currencySymbol }}{{ formatNumber(budget?.spent_amount) }}</span>
            </div>
          </div>
          <div class="col-4">
            <div class="p-3 rounded-3 bg-success-subtle text-center border border-success-subtle">
              <span class="text-success text-xxs text-uppercase fw-bold tracking-wider d-block mb-1">Remaining</span>
              <span class="fw-bold text-success fs-6">{{ currencySymbol }}{{ formatNumber(budget?.remaining_amount) }}</span>
            </div>
          </div>
        </div>

        <!-- Progress Bar in Modal -->
        <div class="mb-4 px-3 py-2.5 rounded-3 bg-light border">
          <div class="d-flex align-items-center justify-content-between text-xs mb-1.5">
            <span class="text-muted fw-medium">Budget Usage</span>
            <span class="fw-bold" :class="isOver ? 'text-danger' : 'text-dark'">
              {{ budget?.actual_percentage || budget?.progress_percentage || 0 }}% used
            </span>
          </div>
          <div class="progress rounded-pill bg-white" style="height: 8px;">
            <div
              class="progress-bar rounded-pill"
              :class="progressBarClass"
              :style="{ width: `${Math.min(100, budget?.progress_percentage || 0)}%` }"
            ></div>
          </div>
        </div>

        <!-- Transactions List Header -->
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2 text-sm">
            <Receipt :size="16" class="text-primary" />
            <span>Recorded Expenses ({{ transactions.length }})</span>
          </h6>
          <span class="text-muted text-xs">Transactions for this budget period</span>
        </div>

        <!-- Loading Transactions -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
          <span class="text-muted small ms-2">Loading transactions...</span>
        </div>

        <!-- Empty Transactions -->
        <div v-else-if="transactions.length === 0" class="text-center py-5 border rounded-3 bg-light">
          <Receipt :size="32" class="text-muted opacity-40 mb-2" />
          <p class="text-muted small mb-0">No expense transactions recorded for this budget period yet.</p>
        </div>

        <!-- Transactions List -->
        <div v-else class="transactions-scroll-list" style="max-height: 280px; overflow-y: auto;">
          <div class="list-group list-group-flush rounded-3 border">
            <div
              v-for="tx in transactions"
              :key="tx.id"
              class="list-group-item d-flex align-items-center justify-content-between py-3 px-3 hover-bg-light transition-all"
            >
              <div class="d-flex align-items-start gap-3">
                <div class="p-2 rounded-3 bg-danger-subtle text-danger mt-0.5">
                  <TrendingDown :size="15" />
                </div>
                <div>
                  <h6 class="fw-semibold text-dark mb-1 text-xs">{{ tx.description || 'Expense Transaction' }}</h6>
                  <div class="d-flex flex-wrap align-items-center gap-2 text-xxs text-muted">
                    <span class="d-flex align-items-center gap-1">
                      <Calendar :size="11" />
                      {{ tx.date }}
                    </span>
                    <span v-if="tx.account_name" class="badge bg-secondary-subtle text-secondary rounded-2 px-2 py-0.5">
                      {{ tx.account_name }}
                    </span>
                    <span v-if="tx.payment_method" class="text-capitalize text-muted">
                      &bull; {{ tx.payment_method }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="text-end ps-3">
                <span class="fw-bold text-danger text-sm">-{{ currencySymbol }}{{ formatNumber(tx.amount) }}</span>
                <div v-if="tx.reference_number" class="text-muted text-xxs mt-0.5">
                  {{ tx.reference_number }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer border-top px-4 py-3 bg-light-subtle d-flex justify-content-end">
        <button type="button" class="btn btn-sm btn-secondary rounded-3 px-3 text-xs" @click="$emit('close')">
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import {
  Tag,
  PieChart,
  Receipt,
  Calendar,
  X,
  TrendingDown,
} from '@lucide/vue';
import { resolveCategoryIcon } from '@/libs/CategoryIcons.js';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  budget: {
    type: Object,
    default: null,
  },
  categories: {
    type: Array,
    default: () => [],
  },
  transactions: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  currencySymbol: {
    type: String,
    default: '৳',
  },
});

defineEmits(['close']);

function formatNumber(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

const categoryObj = computed(() => {
  if (props.budget?.category && (props.budget.category.icon || props.budget.category.color)) {
    return props.budget.category;
  }
  if (props.budget?.category_id && props.categories?.length) {
    const found = props.categories.find((c) => Number(c.id) === Number(props.budget.category_id));
    if (found) return found;
  }
  return props.budget?.category || null;
});

const budgetName = computed(() => {
  if (!props.budget) return 'Overall Budget';
  return categoryObj.value?.name || props.budget.category_name || props.budget.category?.name || 'Overall Budget';
});

const categoryIcon = computed(() => {
  if (!props.budget) return Tag;
  if (!props.budget.category_id && !categoryObj.value) return PieChart;
  return resolveCategoryIcon(categoryObj.value?.icon || categoryObj.value?.name || props.budget.category_name);
});

const categoryColor = computed(() => {
  return categoryObj.value?.color || props.budget?.category?.color || '#3b82f6';
});

const categoryBg = computed(() => {
  const c = categoryColor.value;
  return c ? c + '18' : '#3b82f618';
});

const isOver = computed(() => {
  if (!props.budget) return false;
  const spent = parseFloat(props.budget.spent_amount) || 0;
  const amount = parseFloat(props.budget.amount) || 0;
  return props.budget.is_over_budget || spent > amount;
});

const progressBarClass = computed(() => {
  if (!props.budget) return 'bg-primary';
  const progress = parseFloat(props.budget.progress_percentage) || 0;
  if (isOver.value || progress >= 100) return 'bg-danger';
  if (progress >= 80) return 'bg-warning';
  return 'bg-success';
});

const formattedDate = computed(() => {
  if (!props.budget?.start_date) return '';
  const s = new Date(props.budget.start_date);
  const monthName = s.toLocaleString('default', { month: 'short' });
  const year = s.getFullYear();

  if (props.budget.period === 'monthly') {
    return `${monthName} ${year}`;
  }
  if (props.budget.end_date) {
    const e = new Date(props.budget.end_date);
    const endMonth = e.toLocaleString('default', { month: 'short' });
    if (monthName === endMonth) {
      return `${monthName} ${s.getDate()} - ${e.getDate()}, ${year}`;
    }
    return `${monthName} ${s.getDate()} - ${endMonth} ${e.getDate()}, ${year}`;
  }
  return `${monthName} ${s.getDate()}, ${year}`;
});
</script>

<style scoped lang="scss">
.category-icon-box {
  width: 44px;
  height: 44px;
  min-width: 44px;
}

.text-xxs {
  font-size: 0.68rem;
  letter-spacing: 0.04em;
}

.text-xs {
  font-size: 0.75rem;
}

.text-sm {
  font-size: 0.85rem;
}

.hover-bg-light:hover {
  background-color: #f8fafc;
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
