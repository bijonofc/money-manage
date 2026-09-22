<template>
  <div v-if="modelValue" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3" @click.self="close">
    <div class="modal-card bg-white rounded-4 shadow-xl border-0 overflow-hidden animate-scale-in" style="max-width: 520px; width: 100%;">
      
      <!-- Modal Header -->
      <div class="modal-header-custom d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
          <div class="header-icon-pill rounded-3 p-2.5 d-flex align-items-center justify-content-center bg-primary-subtle text-primary">
            <PieChart :size="20" />
          </div>
          <div>
            <h5 class="fw-bold mb-0 text-dark">{{ editData ? 'Edit Budget' : 'Create Budget' }}</h5>
            <p class="text-muted text-xs mb-0">Set spending limit by category, period, and alert rules</p>
          </div>
        </div>
        <button type="button" class="btn-close-custom btn btn-icon btn-light rounded-circle p-2" @click="close" aria-label="Close">
          <X :size="18" class="text-muted" />
        </button>
      </div>

      <!-- Modal Body Form -->
      <form @submit.prevent="handleSubmit" class="p-4">
        
        <!-- Category Selector (Appsbd UI AbMultiSelect with search & icons) -->
        <div class="mb-3">
          <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5 d-flex align-items-center justify-content-between">
            <span>Category</span>
            <span class="badge bg-light text-secondary border fw-normal">
              {{ form.category_id !== 'all' ? 'Category-specific' : 'Overall Spending' }}
            </span>
          </label>

          <ab-multi-select
            v-model="form.category_id"
            name="category_id"
            :options="categoryOptions"
            label-key="title"
            value-key="val"
            placeholder="Search & select expense category..."
            searchable
            container-class="mb-0 modern-category-select"
          >
            <!-- Selected label slot -->
            <template #singlelabel="{ value }">
              <div v-if="value" class="d-flex align-items-center py-0.5 min-w-0" style="gap: 10px;">
                <div
                  class="cat-icon-badge rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                  :style="{ backgroundColor: (value.color || '#3b82f6') + '20', color: value.color || '#3b82f6' }"
                >
                  <component :is="value.isOverall ? PieChart : resolveCategoryIcon(value.icon || value.title)" :size="15" />
                </div>
                <span class="fw-semibold text-dark text-truncate">{{ value.title }}</span>
              </div>
            </template>

            <!-- Dropdown Option slot -->
            <template #option="{ option, isSelected }">
              <div class="d-flex align-items-center justify-content-between w-100 py-1">
                <div class="d-flex align-items-center min-w-0" style="gap: 10px;">
                  <div
                    class="cat-icon-badge rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                    :class="{ 'is-selected-badge': isSelected }"
                    :style="!isSelected ? { backgroundColor: (option.color || '#3b82f6') + '20', color: option.color || '#3b82f6' } : { color: option.color || '#3b82f6' }"
                  >
                    <component :is="option.isOverall ? PieChart : resolveCategoryIcon(option.icon || option.title)" :size="15" />
                  </div>
                  <div class="min-w-0">
                    <span class="fw-medium text-truncate d-block" :class="isSelected ? 'fw-bold text-white' : 'text-dark'">
                      {{ option.title }}
                    </span>
                    <small v-if="option.isOverall" class="text-xxs d-block lh-1" :class="isSelected ? 'text-white-50' : 'text-muted'">Tracks total overall spending</small>
                  </div>
                </div>
                <span v-if="isSelected" class="badge bg-white text-success rounded-pill px-2.5 py-0.5 text-xxs fw-bold flex-shrink-0 ms-2 shadow-xs">
                  Selected
                </span>
              </div>
            </template>
          </ab-multi-select>
          <div class="form-text text-xxs text-muted mt-1">Only expense categories can be budgeted (income categories are excluded)</div>
        </div>

        <!-- Budget Limit & Period Row -->
        <div class="row g-3 mb-3">
          <div class="col-7">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
              Budget Limit ({{ currencySymbol }}) <span class="text-danger">*</span>
            </label>
            <div class="input-group input-group-modern">
              <span class="input-group-text bg-light border-end-0 fw-bold text-primary ps-3">
                {{ currencySymbol }}
              </span>
              <input
                v-model.number="form.amount"
                type="number"
                step="0.01"
                min="0.01"
                class="form-control form-control-modern border-start-0 ps-2 fw-semibold"
                placeholder="e.g. 15000"
                required
              />
            </div>
          </div>

          <div class="col-5">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
              Period <span class="text-danger">*</span>
            </label>
            <div class="input-group input-group-modern">
              <select v-model="form.period" class="form-select form-select-modern" required @change="onPeriodChange">
                <option value="monthly">Monthly</option>
                <option value="weekly">Weekly</option>
                <option value="yearly">Yearly</option>
                <option value="daily">Daily</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Date Range Row -->
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
              Start Date <span class="text-danger">*</span>
            </label>
            <div class="input-group input-group-modern">
              <span class="input-group-text bg-light border-end-0 text-muted ps-2.5">
                <Calendar :size="15" />
              </span>
              <input
                v-model="form.start_date"
                type="date"
                class="form-control form-control-modern border-start-0 ps-2"
                required
                @change="onPeriodChange"
              />
            </div>
          </div>

          <div class="col-6">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
              End Date <span class="text-muted fw-normal text-capitalize">(Optional)</span>
            </label>
            <div class="input-group input-group-modern">
              <span class="input-group-text bg-light border-end-0 text-muted ps-2.5">
                <Calendar :size="15" />
              </span>
              <input
                v-model="form.end_date"
                type="date"
                class="form-control form-control-modern border-start-0 ps-2"
              />
            </div>
          </div>
        </div>

        <!-- Alert Threshold Section -->
        <div class="alert-threshold-card p-3 rounded-3 mb-3 border bg-light-subtle">
          <div class="d-flex align-items-center mb-2">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-0 d-flex align-items-center" style="gap: 8px;">
              <AlertTriangle :size="15" class="text-warning flex-shrink-0" />
              <span>Alert Threshold</span>
            </label>
          </div>

          <div class="row g-2 align-items-center mb-2">
            <div class="col-6">
              <div class="input-group input-group-modern input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                  <Percent :size="13" />
                </span>
                <input
                  v-model.number="form.alert_threshold"
                  type="number"
                  min="1"
                  max="100"
                  step="1"
                  class="form-control form-control-sm border-start-0 ps-2 fw-semibold"
                  placeholder="80"
                  required
                />
              </div>
            </div>
            <div class="col-6">
              <!-- Quick Threshold Preset Chips -->
              <div class="d-flex align-items-center gap-1">
                <button
                  v-for="preset in [70, 80, 90, 100]"
                  :key="preset"
                  type="button"
                  class="btn btn-xs rounded-2 flex-fill py-1 transition-all"
                  :class="form.alert_threshold === preset ? 'btn-primary text-white fw-bold shadow-xs' : 'btn-white border text-muted'"
                  @click="form.alert_threshold = preset"
                >
                  {{ preset }}%
                </button>
              </div>
            </div>
          </div>

          <!-- Threshold Progress Preview Bar -->
          <div class="threshold-bar-container mt-2">
            <div class="progress rounded-pill bg-white border" style="height: 6px;">
              <div
                class="progress-bar bg-warning rounded-pill transition-all"
                :style="{ width: `${Math.min(100, Math.max(0, form.alert_threshold || 0))}%` }"
              ></div>
            </div>
          </div>
          <div class="form-text text-xxs text-muted mt-1.5">
            You will receive warnings once spending reaches <strong>{{ form.alert_threshold }}%</strong> of the allocated budget.
          </div>
        </div>

        <!-- Status Toggle Section (Using Appsbd UI ab-toggle) -->
        <div class="status-toggle-card p-3 rounded-3 mb-2 border d-flex align-items-center justify-content-between" :class="form.status === 'A' ? 'bg-success-subtle-light' : 'bg-light'">
          <div>
            <div class="d-flex align-items-center gap-2">
              <span class="text-xs fw-bold text-uppercase tracking-wider text-dark">Budget Status</span>
              <span
                class="badge rounded-pill px-2 py-0.5 text-xxs fw-semibold"
                :class="form.status === 'A' ? 'bg-success text-white' : 'bg-secondary text-white'"
              >
                {{ form.status === 'A' ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <p class="text-muted text-xxs mb-0 mt-0.5">
              {{ form.status === 'A' ? 'Budget is actively tracked and notifications are enabled' : 'Budget is paused and spending warnings are suspended' }}
            </p>
          </div>

          <div class="flex-shrink-0 ms-3">
            <ab-toggle
              v-model="form.status"
              name="status"
              :true-value="'A'"
              :false-value="'I'"
              container-class="mb-0"
              size="md"
            />
          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="d-flex align-items-center justify-content-end gap-2.5 mt-4 pt-3 border-top">
          <ab-button
            type="button"
            color="light"
            @click="close"
          >
            Cancel
          </ab-button>
          <ab-button
            type="submit"
            color="primary"
            :is-animated="saving"
            :disabled="saving"
          >
            {{ editData ? 'Update Budget' : 'Save Budget' }}
          </ab-button>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import {
  Tag,
  PieChart,
  Calendar,
  AlertTriangle,
  Percent,
  X,
} from '@lucide/vue';
import { resolveCategoryIcon } from '@/libs/CategoryIcons.js';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  editData: {
    type: Object,
    default: null,
  },
  categories: {
    type: Array,
    default: () => [],
  },
  currencySymbol: {
    type: String,
    default: '৳',
  },
  saving: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue', 'save']);

const form = ref({
  category_id: 'all',
  amount: '',
  period: 'monthly',
  start_date: new Date().toISOString().split('T')[0],
  end_date: '',
  alert_threshold: 80,
  status: 'A',
});

// 1. Filter out all income categories — only expense categories can be budgeted
const expenseCategories = computed(() => {
  return (props.categories || []).filter((c) => !c.type || c.type === 'expense');
});

// 2. Build options array with overall option + expense categories
const categoryOptions = computed(() => {
  const options = [
    {
      val: 'all',
      title: 'All Categories (Overall Budget)',
      icon: 'pie-chart',
      color: '#3b82f6',
      isOverall: true,
    },
  ];

  expenseCategories.value.forEach((cat) => {
    options.push({
      val: Number(cat.id),
      title: cat.name,
      icon: cat.icon || cat.name,
      color: cat.color || '#64748b',
      isOverall: false,
    });
  });

  return options;
});

function calculateEndDate(startDateStr, period) {
  if (!startDateStr) return '';
  const [year, month, day] = startDateStr.split('-').map(Number);
  const date = new Date(year, month - 1, day);

  if (period === 'monthly') {
    const lastDay = new Date(year, month, 0);
    const m = String(lastDay.getMonth() + 1).padStart(2, '0');
    const d = String(lastDay.getDate()).padStart(2, '0');
    return `${lastDay.getFullYear()}-${m}-${d}`;
  } else if (period === 'weekly') {
    const end = new Date(date);
    end.setDate(end.getDate() + 6);
    const m = String(end.getMonth() + 1).padStart(2, '0');
    const d = String(end.getDate()).padStart(2, '0');
    return `${end.getFullYear()}-${m}-${d}`;
  } else if (period === 'yearly') {
    return `${year}-12-31`;
  } else if (period === 'daily') {
    return startDateStr;
  }
  return '';
}

function onPeriodChange() {
  if (form.value.start_date && !props.editData) {
    form.value.end_date = calculateEndDate(form.value.start_date, form.value.period);
  }
}

watch(
  () => props.modelValue,
  (isOpen) => {
    if (isOpen) {
      if (props.editData) {
        form.value = {
          category_id: props.editData.category_id ? Number(props.editData.category_id) : 'all',
          amount: props.editData.amount ?? '',
          period: props.editData.period || 'monthly',
          start_date: props.editData.start_date || new Date().toISOString().split('T')[0],
          end_date: props.editData.end_date || '',
          alert_threshold: props.editData.alert_threshold ? Number(props.editData.alert_threshold) : 80,
          status: props.editData.status || (props.editData.is_active === false ? 'I' : 'A'),
        };
      } else {
        const todayStr = new Date().toISOString().split('T')[0];
        form.value = {
          category_id: 'all',
          amount: '',
          period: 'monthly',
          start_date: todayStr,
          end_date: calculateEndDate(todayStr, 'monthly'),
          alert_threshold: 80,
          status: 'A',
        };
      }
    }
  },
  { immediate: true }
);

function close() {
  emit('update:modelValue', false);
}

function handleSubmit() {
  emit('save', {
    ...form.value,
    category_id: form.value.category_id === 'all' || !form.value.category_id ? null : Number(form.value.category_id),
  });
}
</script>

<style scoped lang="scss">
.modal-backdrop-custom {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  z-index: 1050;
  backdrop-filter: blur(4px);
  overflow-y: auto;
  overflow-x: hidden;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 2.5rem 1rem;
  -webkit-overflow-scrolling: touch;
}

.modal-card {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  border: 1px solid rgba(226, 232, 240, 0.8);
  margin: auto;
}

.header-icon-pill {
  width: 42px;
  height: 42px;
}

.cat-icon-badge {
  width: 28px;
  height: 28px;
  min-width: 28px;
  transition: all 0.15s ease;

  &.is-selected-badge {
    background-color: #ffffff !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
    border: 1px solid rgba(0, 0, 0, 0.06);

    [data-bs-theme="dark"] & {
      background-color: #1e293b !important;
      border-color: #334155;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
    }
  }
}

.modern-category-select {
  :deep(.multiselect) {
    border-radius: 12px;
    min-height: 44px;
    border: 1.5px solid #e2e8f0;
    transition: all 0.2s ease;

    &:focus-within,
    &.is-active {
      border-color: #137035;
      box-shadow: 0 0 0 3px rgba(19, 112, 53, 0.12);
    }
  }

  :deep(.multiselect-dropdown) {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
    padding: 6px;
    max-height: 260px;
  }

  :deep(.multiselect-option) {
    border-radius: 8px;
    padding: 8px 10px;
    margin-bottom: 2px;
    transition: background-color 0.15s ease;

    &.is-pointed {
      background-color: #f8fafc !important;
      color: inherit !important;
    }

    &.is-selected {
      background-color: #ecfdf5 !important;
      color: #065f46 !important;
    }

    &.is-selected.is-pointed,
    &.is-selected:hover {
      background-color: #d1fae5 !important;
      color: #065f46 !important;
    }
  }

  [data-bs-theme="dark"] & {
    :deep(.multiselect-option) {
      &.is-pointed {
        background-color: #1e293b !important;
        color: #f8fafc !important;
      }

      &.is-selected {
        background-color: rgba(16, 185, 129, 0.15) !important;
        color: #34d399 !important;
      }

      &.is-selected.is-pointed,
      &.is-selected:hover {
        background-color: rgba(16, 185, 129, 0.25) !important;
        color: #34d399 !important;
      }
    }
  }
}

.input-group-modern {
  border-radius: 12px;
  overflow: hidden;
  border: 1.5px solid #e2e8f0;
  transition: all 0.2s ease;

  &:focus-within {
    border-color: #137035;
    box-shadow: 0 0 0 3px rgba(19, 112, 53, 0.12);
  }

  [data-bs-theme="dark"] & {
    border-color: #334155;
    background: #0b111e;

    &:focus-within {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
  }

  .input-group-text {
    border: none;
  }

  .form-control,
  .form-select {
    border: none;
    box-shadow: none;
    font-size: 0.92rem;

    &:focus {
      box-shadow: none;
    }
  }
}

.alert-threshold-card {
  border-color: rgba(245, 158, 11, 0.25) !important;
  background-color: #fffbeb;

  [data-bs-theme="dark"] & {
    background-color: rgba(245, 158, 11, 0.08);
    border-color: rgba(245, 158, 11, 0.2) !important;
  }
}

.bg-success-subtle-light {
  background-color: #f0fdf4;
  border-color: #bbf7d0 !important;

  [data-bs-theme="dark"] & {
    background-color: rgba(16, 185, 129, 0.1);
    border-color: rgba(16, 185, 129, 0.2) !important;
  }
}

.btn-white {
  background-color: #ffffff;
  &:hover {
    background-color: #f8fafc;
  }
}

.btn-xs {
  font-size: 0.72rem;
  padding: 0.2rem 0.4rem;
}

.shadow-xs {
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.text-xs { font-size: 0.75rem; }
.text-xxs { font-size: 0.6875rem; }
.tracking-wider { letter-spacing: 0.05em; }

.animate-scale-in {
  animation: scaleIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.96) translateY(8px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>
