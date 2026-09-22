<template>
  <div class="card border-0 shadow-sm rounded-4 h-100 p-4 budget-card d-flex flex-column bg-white">
    <!-- Card Header -->
    <div class="d-flex align-items-start justify-content-between mb-3 gap-2">
      <!-- Icon + Category Title + Period -->
      <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
        <!-- Category Icon Box -->
        <div
          class="category-icon-box rounded-3 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0"
          :style="{ backgroundColor: categoryBg, color: categoryColor }"
        >
          <component :is="categoryIcon" :size="20" />
        </div>

        <!-- Title & Subtitle -->
        <div class="min-w-0 flex-grow-1">
          <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <h5 class="fw-bold mb-0 text-dark category-title text-break" :title="budgetName">
              {{ budgetName }}
            </h5>
            <span
              v-if="budget.status === 'I' || budget.is_active === false"
              class="badge bg-secondary-subtle text-secondary border text-xxs px-1.5 py-0.5 rounded-pill fw-semibold"
            >
              Inactive
            </span>
          </div>
          <div
            class="d-flex align-items-center gap-1.5 text-muted text-xs text-nowrap cursor-help"
            :title="periodTooltip"
          >
            <Calendar :size="12" class="opacity-70 flex-shrink-0" />
            <span>{{ formattedDate }}</span>
          </div>
        </div>
      </div>

      <!-- Action Buttons (Edit / Delete) -->
      <div class="d-flex align-items-center gap-1 flex-shrink-0 ms-2">
        <button
          type="button"
          class="btn btn-icon btn-action rounded-circle text-muted"
          title="Edit Budget"
          @click="$emit('edit', budget)"
        >
          <Pencil :size="14" />
        </button>
        <button
          type="button"
          class="btn btn-icon btn-action rounded-circle text-muted hover-danger"
          title="Delete Budget"
          @click="$emit('delete', budget.id)"
        >
          <Trash2 :size="14" />
        </button>
      </div>
    </div>

    <!-- Main Budget Target & Spent Metrics -->
    <div class="my-auto py-2">
      <!-- Target vs Spent Row -->
      <div class="d-flex align-items-end justify-content-between mb-2">
        <div>
          <span class="text-muted text-xxs fw-bold text-uppercase tracking-wider d-block mb-1">
            Budget Target
          </span>
          <span class="fs-4 fw-bold text-dark">
            {{ currencySymbol }}{{ formatNumber(budget.amount) }}
          </span>
        </div>
        <div class="text-end">
          <span class="text-muted text-xxs fw-bold text-uppercase tracking-wider d-block mb-1">
            Spent
          </span>
          <span class="fs-5 fw-bold" :class="isOver ? 'text-danger' : 'text-secondary'">
            {{ currencySymbol }}{{ formatNumber(budget.spent_amount || 0) }}
          </span>
        </div>
      </div>

      <!-- Slim Progress Bar -->
      <div class="progress rounded-pill bg-light mb-2" style="height: 8px;">
        <div
          class="progress-bar rounded-pill transition-all"
          :class="progressBarClass"
          :style="{ width: `${Math.min(100, budget.progress_percentage || 0)}%` }"
        ></div>
      </div>

      <!-- Remaining / Over by & Percentage Row -->
      <div class="d-flex align-items-center justify-content-between text-xs pt-1">
        <span v-if="isOver" class="text-danger fw-semibold d-flex align-items-center gap-1">
          <AlertTriangle :size="13" />
          Over by {{ currencySymbol }}{{ formatNumber(overAmount) }}
        </span>
        <span v-else class="text-success fw-medium d-flex align-items-center gap-1">
          <Check :size="13" />
          {{ currencySymbol }}{{ formatNumber(budget.remaining_amount) }} remaining
        </span>

        <span class="fw-bold" :class="isOver ? 'text-danger' : 'text-muted'">
          {{ percentUsed }}% used
        </span>
      </div>
    </div>

    <!-- Card Action Button -->
    <div class="mt-3 pt-2">
      <button
        type="button"
        class="btn btn-sm btn-light border w-100 rounded-3 py-2 text-xs fw-semibold text-secondary d-flex align-items-center justify-content-center gap-2 hover-breakdown transition-all"
        @click="$emit('view-expenses', budget)"
      >
        <Receipt :size="15" class="text-primary" />
        <span>View Expenses Breakdown</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import {
  Calendar,
  Trash2,
  Pencil,
  AlertTriangle,
  Check,
  Receipt,
  Tag,
  PieChart,
} from '@lucide/vue';
import { resolveCategoryIcon } from '@/libs/CategoryIcons.js';

const props = defineProps({
  budget: {
    type: Object,
    required: true,
  },
  categories: {
    type: Array,
    default: () => [],
  },
  currencySymbol: {
    type: String,
    default: '৳',
  },
});

defineEmits(['view-expenses', 'edit', 'delete']);

function formatNumber(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

const categoryObj = computed(() => {
  if (props.budget.category && (props.budget.category.icon || props.budget.category.color)) {
    return props.budget.category;
  }
  if (props.budget.category_id && props.categories?.length) {
    const found = props.categories.find((c) => Number(c.id) === Number(props.budget.category_id));
    if (found) return found;
  }
  return props.budget.category || null;
});

const budgetName = computed(() => {
  return categoryObj.value?.name || props.budget.category_name || props.budget.category?.name || 'Overall Budget';
});

const categoryIcon = computed(() => {
  if (!props.budget.category_id && !categoryObj.value) return PieChart;
  return resolveCategoryIcon(categoryObj.value?.icon || categoryObj.value?.name || props.budget.category_name);
});

const categoryColor = computed(() => {
  return categoryObj.value?.color || props.budget.category?.color || '#3b82f6';
});

const categoryBg = computed(() => {
  const c = categoryColor.value;
  return c ? c + '18' : '#3b82f618';
});

const isOver = computed(() => {
  const spent = parseFloat(props.budget.spent_amount) || 0;
  const amount = parseFloat(props.budget.amount) || 0;
  return props.budget.is_over_budget || spent > amount;
});

const overAmount = computed(() => {
  if (props.budget.over_amount) return props.budget.over_amount;
  const spent = parseFloat(props.budget.spent_amount) || 0;
  const amount = parseFloat(props.budget.amount) || 0;
  return Math.max(0, spent - amount);
});

const percentUsed = computed(() => {
  return props.budget.actual_percentage ?? props.budget.progress_percentage ?? 0;
});

const progressBarClass = computed(() => {
  const progress = parseFloat(props.budget.progress_percentage) || 0;
  if (isOver.value || progress >= 100) return 'bg-danger';
  if (progress >= 80) return 'bg-warning';
  return 'bg-success';
});

const formattedDate = computed(() => {
  if (!props.budget.start_date) return '';
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

const periodTooltip = computed(() => {
  const period = props.budget.period ? props.budget.period.charAt(0).toUpperCase() + props.budget.period.slice(1) : 'Monthly';
  if (props.budget.start_date && props.budget.end_date) {
    return `${period} Budget: ${props.budget.start_date} to ${props.budget.end_date}`;
  }
  if (props.budget.start_date) {
    return `${period} Budget: From ${props.budget.start_date}`;
  }
  return `${period} Budget`;
});
</script>

<style scoped lang="scss">
.budget-card {
  border: 1px solid rgba(0, 0, 0, 0.06) !important;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08) !important;
    border-color: rgba(0, 0, 0, 0.1) !important;
  }
}

.category-icon-box {
  width: 44px;
  height: 44px;
  min-width: 44px;
}

.category-title {
  font-size: 1.05rem;
  line-height: 1.3;
}

.btn-action {
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: none;
  transition: background-color 0.15s ease, color 0.15s ease;

  &:hover {
    background-color: rgba(0, 0, 0, 0.06);
    color: var(--bs-primary) !important;
  }

  &.hover-danger:hover {
    background-color: rgba(var(--bs-danger-rgb), 0.1);
    color: var(--bs-danger) !important;
  }
}

.hover-breakdown {
  background-color: #f8fafc;
  border-color: #e2e8f0 !important;
  &:hover {
    background-color: #f1f5f9 !important;
    color: var(--bs-primary) !important;
    border-color: rgba(var(--bs-primary-rgb), 0.3) !important;
  }
}

.text-xxs {
  font-size: 0.68rem;
  letter-spacing: 0.04em;
}

.text-xs {
  font-size: 0.75rem;
}

.cursor-help {
  cursor: help;
}
</style>
