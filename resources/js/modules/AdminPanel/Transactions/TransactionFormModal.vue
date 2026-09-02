<template>
  <div v-if="modelValue" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3" @click.self="close">
    <div class="modal-card bg-white rounded-4 shadow-xl border-0 overflow-hidden animate-scale-in" style="max-width: 560px; width: 100%;">
      
      <!-- Modal Header with Dynamic Segmented Switcher -->
      <div class="modal-header-custom px-4 pt-4 pb-3 border-bottom">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="d-flex align-items-center gap-2">
            <div class="type-indicator-dot" :class="'dot-' + form.transaction_type"></div>
            <h5 class="fw-bold mb-0 text-dark">
              {{ editData ? 'Edit Transaction' : 'Record Transaction' }}
            </h5>
          </div>
          <button type="button" class="btn-close-custom btn btn-icon btn-light rounded-circle p-2" @click="close" aria-label="Close">
            <X :size="18" class="text-muted" />
          </button>
        </div>

        <!-- 3-Way Segmented Tabs (Large, thumb-friendly cards) -->
        <div class="segmented-control p-1 rounded-3 bg-light d-flex align-items-center gap-1">
          <button
            type="button"
            class="segmented-tab flex-fill py-2 px-3 rounded-3 d-flex align-items-center justify-content-center gap-2 transition-all fw-bold text-xs"
            :class="{ 'tab-expense-active': form.transaction_type === 'expense' }"
            @click="setTransactionType('expense')"
          >
            <ArrowUpRight :size="16" />
            <span>EXPENSE</span>
          </button>

          <button
            type="button"
            class="segmented-tab flex-fill py-2 px-3 rounded-3 d-flex align-items-center justify-content-center gap-2 transition-all fw-bold text-xs"
            :class="{ 'tab-income-active': form.transaction_type === 'income' }"
            @click="setTransactionType('income')"
          >
            <ArrowDownLeft :size="16" />
            <span>INCOME</span>
          </button>

          <button
            type="button"
            class="segmented-tab flex-fill py-2 px-3 rounded-3 d-flex align-items-center justify-content-center gap-2 transition-all fw-bold text-xs"
            :class="{ 'tab-transfer-active': form.transaction_type === 'transfer' }"
            @click="setTransactionType('transfer')"
          >
            <ArrowLeftRight :size="16" />
            <span>TRANSFER</span>
          </button>
        </div>
      </div>

      <!-- Modal Body Form -->
      <form @submit.prevent="handleSubmit" class="p-4">
        
        <!-- HERO AMOUNT SECTION -->
        <div class="hero-amount-card p-3 rounded-4 mb-4 border text-center transition-all" :class="'hero-theme-' + form.transaction_type">
          <label class="text-xs fw-bold text-uppercase tracking-wider opacity-75 mb-1 d-block">
            {{ form.transaction_type === 'expense' ? 'Expense Amount' : form.transaction_type === 'income' ? 'Income Amount' : 'Transfer Amount' }} <span class="text-danger">*</span>
          </label>
          
          <div class="amount-input-wrapper d-inline-flex align-items-baseline justify-content-center">
            <span class="currency-symbol display-6 fw-bold me-1 text-dark opacity-75">৳</span>
            <input
              ref="amountInputRef"
              v-model="rawAmount"
              type="text"
              inputmode="decimal"
              class="hero-amount-input fw-bolder text-center"
              placeholder="0.00"
              required
              autofocus
              @input="onAmountInput"
            />
          </div>

          <!-- Quick BDT Increment Chips -->
          <div class="quick-chips d-flex align-items-center justify-content-center gap-1.5 flex-wrap mt-2.5">
            <button
              v-for="chip in quickAmountChips"
              :key="chip.val"
              type="button"
              class="btn btn-sm btn-quick-chip rounded-pill px-2.5 py-1 text-xxs fw-semibold transition-all"
              @click="addQuickAmount(chip.val)"
            >
              +৳{{ chip.label }}
            </button>
            <button
              v-if="parseFloat(rawAmount) > 0"
              type="button"
              class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0.5 text-xxs fw-normal"
              @click="rawAmount = ''"
            >
              Clear
            </button>
          </div>
        </div>

        <!-- DYNAMIC FIELDS ACCORDING TO TYPE -->

        <!-- CASE 1 & 2: EXPENSE OR INCOME -->
        <div v-if="form.transaction_type !== 'transfer'" class="row g-3 mb-3">
          
          <!-- Dynamic Account Label: "From Account" for Expense vs "To Account" for Income -->
          <div class="col-12 col-sm-6">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
              <span>{{ form.transaction_type === 'expense' ? 'From Account' : 'To Account' }}</span>
              <span class="text-danger ms-1">*</span>
            </label>
            <div class="input-group input-group-modern">
              <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                <Wallet :size="16" />
              </span>
              <select v-model="form.account_id" class="form-select form-control-modern border-start-0 ps-2" required>
                <option value="" disabled>Select account...</option>
                <option v-for="acc in accountList" :key="acc.id" :value="acc.id">
                  {{ acc.name }} (৳{{ formatNumber(acc.balance) }})
                </option>
              </select>
            </div>
          </div>

          <!-- Category Selector -->
          <div class="col-12 col-sm-6">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5 d-flex align-items-center justify-content-between">
              <span>Category <span class="text-danger">*</span></span>
              <span v-if="filteredCategories.length === 0" class="text-xxs text-muted">No categories</span>
            </label>
            <div class="input-group input-group-modern">
              <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                <Tag :size="16" />
              </span>
              <select v-model="form.category_id" class="form-select form-control-modern border-start-0 ps-2" required>
                <option :value="null" disabled>Select category...</option>
                <option v-for="cat in filteredCategories" :key="cat.id" :value="cat.id">
                  {{ cat.icon ? cat.icon + ' ' : '' }}{{ cat.name }}
                </option>
              </select>
            </div>
          </div>

        </div>

        <!-- CASE 3: TRANSFER (From Account ➡️ To Account) -->
        <div v-else class="transfer-box p-3 rounded-3 bg-light-subtle border mb-3">
          <div class="row g-2 align-items-center">
            
            <div class="col-12 col-md-5">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">
                From Account <span class="text-danger">*</span>
              </label>
              <div class="input-group input-group-modern input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                  <Building2 :size="14" />
                </span>
                <select v-model="form.from_account_id" class="form-select form-control-sm border-start-0" required>
                  <option value="" disabled>Source account...</option>
                  <option v-for="acc in accountList" :key="'from-' + acc.id" :value="acc.id">
                    {{ acc.name }} (৳{{ formatNumber(acc.balance) }})
                  </option>
                </select>
              </div>
            </div>

            <div class="col-12 col-md-2 text-center py-1">
              <div class="transfer-arrow-badge mx-auto rounded-circle d-flex align-items-center justify-content-center text-primary bg-white shadow-sm border">
                <ArrowRight :size="16" class="d-none d-md-block" />
                <ArrowDown :size="16" class="d-md-none" />
              </div>
            </div>

            <div class="col-12 col-md-5">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">
                To Account <span class="text-danger">*</span>
              </label>
              <div class="input-group input-group-modern input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                  <Smartphone :size="14" />
                </span>
                <select v-model="form.account_id" class="form-select form-control-sm border-start-0" required>
                  <option value="" disabled>Destination account...</option>
                  <option v-for="acc in accountList" :key="'to-' + acc.id" :value="acc.id" :disabled="acc.id === form.from_account_id">
                    {{ acc.name }} (৳{{ formatNumber(acc.balance) }})
                  </option>
                </select>
              </div>
            </div>

          </div>

          <!-- Transfer Duplicate Warning -->
          <div v-if="form.from_account_id && form.account_id && form.from_account_id === form.account_id" class="text-danger text-xxs mt-2 d-flex align-items-center gap-1">
            <AlertCircle :size="13" />
            <span>Source and destination accounts must be different.</span>
          </div>
        </div>

        <!-- DATE & SHORTCUTS -->
        <div class="row g-3 mb-3">
          <div class="col-12 col-sm-6">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5 d-flex align-items-center justify-content-between">
              <span>Date <span class="text-danger">*</span></span>
              <div class="d-flex gap-1">
                <button
                  type="button"
                  class="btn-date-preset badge border-0 px-2 py-0.5 text-xxs fw-medium transition-all"
                  :class="isTodaySelected ? 'bg-primary text-white' : 'bg-light text-muted'"
                  @click="setDateToday"
                >
                  Today
                </button>
                <button
                  type="button"
                  class="btn-date-preset badge border-0 px-2 py-0.5 text-xxs fw-medium transition-all"
                  :class="isYesterdaySelected ? 'bg-primary text-white' : 'bg-light text-muted'"
                  @click="setDateYesterday"
                >
                  Yesterday
                </button>
              </div>
            </label>
            <div class="input-group input-group-modern">
              <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                <Calendar :size="16" />
              </span>
              <input
                v-model="form.date"
                type="date"
                class="form-control form-control-modern border-start-0 ps-2"
                required
              />
            </div>
          </div>

          <!-- NOTE / DESCRIPTION -->
          <div class="col-12 col-sm-6">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
              Note (Optional)
            </label>
            <div class="input-group input-group-modern">
              <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                <FileText :size="16" />
              </span>
              <input
                v-model="form.description"
                type="text"
                class="form-control form-control-modern border-start-0 ps-2"
                :placeholder="notePlaceholder"
              />
            </div>
          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="d-flex align-items-center justify-content-end gap-2.5 mt-4 pt-3 border-top">
          <button
            type="button"
            class="btn btn-light rounded-pill px-4 py-2 text-sm fw-semibold text-secondary"
            @click="close"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="btn rounded-pill px-4 py-2 text-sm fw-semibold shadow-sm d-flex align-items-center gap-2"
            :class="submitButtonClass"
            :disabled="saving || !isFormValid"
          >
            <span v-if="saving" class="spinner-border spinner-border-sm" role="status"></span>
            <Check v-else :size="16" />
            <span>{{ saving ? 'Saving...' : submitButtonText }}</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import {
  ArrowUpRight,
  ArrowDownLeft,
  ArrowLeftRight,
  ArrowRight,
  ArrowDown,
  Wallet,
  Building2,
  Smartphone,
  Tag,
  Calendar,
  FileText,
  Check,
  X,
  AlertCircle,
} from '@lucide/vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  editData: {
    type: Object,
    default: null,
  },
  accountList: {
    type: Array,
    default: () => [],
  },
  categoryList: {
    type: Array,
    default: () => [],
  },
  saving: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue', 'save']);

const amountInputRef = ref(null);
const rawAmount = ref('');

const form = ref({
  transaction_type: 'expense',
  amount: '',
  account_id: '',
  from_account_id: '',
  category_id: null,
  date: new Date().toISOString().split('T')[0],
  description: '',
});

const quickAmountChips = [
  { label: '100', val: 100 },
  { label: '500', val: 500 },
  { label: '1,000', val: 1000 },
  { label: '2,000', val: 2000 },
  { label: '5,000', val: 5000 },
];

function formatNumber(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function getTodayString() {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function getYesterdayString() {
  const d = new Date();
  d.setDate(d.getDate() - 1);
  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

const isTodaySelected = computed(() => form.value.date === getTodayString());
const isYesterdaySelected = computed(() => form.value.date === getYesterdayString());

function setDateToday() {
  form.value.date = getTodayString();
}

function setDateYesterday() {
  form.value.date = getYesterdayString();
}

function setTransactionType(type) {
  form.value.transaction_type = type;

  // Set default category according to type if needed
  if (type === 'transfer') {
    form.value.category_id = null;
    // Set default from and to account if available
    if (props.accountList.length >= 2) {
      if (!form.value.from_account_id) form.value.from_account_id = props.accountList[0].id;
      if (!form.value.account_id || form.value.account_id === form.value.from_account_id) {
        form.value.account_id = props.accountList[1].id;
      }
    }
  } else {
    // Select first appropriate category
    const relevantCats = filteredCategories.value;
    if (relevantCats.length > 0 && (!form.value.category_id || !relevantCats.some(c => c.id === form.value.category_id))) {
      form.value.category_id = relevantCats[0].id;
    }
  }
}

const filteredCategories = computed(() => {
  if (!props.categoryList || props.categoryList.length === 0) return [];
  if (form.value.transaction_type === 'income') {
    return props.categoryList.filter(c => c.type === 'income' || c.category_type === 'income' || !c.type);
  } else if (form.value.transaction_type === 'expense') {
    return props.categoryList.filter(c => c.type === 'expense' || c.category_type === 'expense' || !c.type);
  }
  return [];
});

const notePlaceholder = computed(() => {
  switch (form.value.transaction_type) {
    case 'expense': return 'What was this for? (e.g. Lunch at Dhanmondi)';
    case 'income': return 'What was this for? (e.g. Monthly Salary, Freelance)';
    case 'transfer': return 'e.g. ATM cash withdrawal, Bank to bKash';
    default: return 'Add note or details';
  }
});

const submitButtonText = computed(() => {
  if (props.editData) return 'Update Transaction';
  switch (form.value.transaction_type) {
    case 'expense': return 'Save Expense';
    case 'income': return 'Save Income';
    case 'transfer': return 'Save Transfer';
    default: return 'Save Transaction';
  }
});

const submitButtonClass = computed(() => {
  switch (form.value.transaction_type) {
    case 'expense': return 'btn-danger text-white';
    case 'income': return 'btn-primary text-white';
    case 'transfer': return 'btn-primary text-white';
    default: return 'btn-primary text-white';
  }
});

const isFormValid = computed(() => {
  const amt = parseFloat(rawAmount.value);
  if (isNaN(amt) || amt <= 0) return false;
  if (!form.value.date) return false;
  if (form.value.transaction_type === 'transfer') {
    return (
      form.value.from_account_id &&
      form.value.account_id &&
      form.value.from_account_id !== form.value.account_id
    );
  }
  return Boolean(form.value.account_id && form.value.category_id);
});

function onAmountInput(e) {
  // Allow only valid numbers and at most 2 decimal digits
  let val = e.target.value.replace(/[^0-9.]/g, '');
  const parts = val.split('.');
  if (parts.length > 2) {
    val = parts[0] + '.' + parts.slice(1).join('');
  }
  rawAmount.value = val;
  form.value.amount = parseFloat(val) || 0;
}

function addQuickAmount(delta) {
  const current = parseFloat(rawAmount.value) || 0;
  const next = current + delta;
  rawAmount.value = next.toString();
  form.value.amount = next;
}

watch(
  () => props.modelValue,
  (open) => {
    if (open) {
      if (props.editData) {
        form.value = {
          transaction_type: props.editData.transaction_type || 'expense',
          amount: props.editData.amount || '',
          account_id: props.editData.account_id || '',
          from_account_id: props.editData.from_account_id || '',
          category_id: props.editData.category_id || null,
          date: props.editData.date || getTodayString(),
          description: props.editData.description || '',
        };
        rawAmount.value = props.editData.amount ? props.editData.amount.toString() : '';
      } else {
        form.value = {
          transaction_type: 'expense',
          amount: '',
          account_id: props.accountList[0]?.id || '',
          from_account_id: props.accountList[0]?.id || '',
          category_id: filteredCategories.value[0]?.id || null,
          date: getTodayString(),
          description: '',
        };
        rawAmount.value = '';
      }
      nextTick(() => {
        if (amountInputRef.value) {
          amountInputRef.value.focus();
        }
      });
    }
  },
  { immediate: true }
);

function close() {
  emit('update:modelValue', false);
}

function handleSubmit() {
  if (!isFormValid.value) return;
  const payload = {
    ...form.value,
    amount: parseFloat(rawAmount.value) || 0,
  };
  emit('save', payload);
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

/* Type Indicator Dot */
.type-indicator-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;

  &.dot-expense { background-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2); }
  &.dot-income { background-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2); }
  &.dot-transfer { background-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); }
}

/* Segmented Control Tabs */
.segmented-control {
  background-color: #f1f5f9;
}

.segmented-tab {
  background: transparent;
  border: none;
  color: #64748b;
  cursor: pointer;

  &:hover {
    color: #1e293b;
  }

  &.tab-expense-active {
    background: #ffffff;
    color: #ef4444;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.15);
  }

  &.tab-income-active {
    background: #ffffff;
    color: #137035;
    box-shadow: 0 2px 6px rgba(19, 112, 53, 0.15);
  }

  &.tab-transfer-active {
    background: #ffffff;
    color: #2563eb;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15);
  }
}

/* Hero Amount Section */
.hero-amount-card {
  background-color: #f8fafc;
  border-color: #e2e8f0 !important;

  &.hero-theme-expense {
    background: linear-gradient(180deg, #fff5f5 0%, #ffffff 100%);
    border-color: #fecaca !important;
  }

  &.hero-theme-income {
    background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
    border-color: #bbf7d0 !important;
  }

  &.hero-theme-transfer {
    background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%);
    border-color: #bfdbfe !important;
  }
}

.hero-amount-input {
  border: none;
  background: transparent;
  font-size: 2.25rem;
  line-height: 1.1;
  color: #0f172a;
  outline: none;
  max-width: 260px;
  width: 100%;

  &::placeholder {
    color: #94a3b8;
    opacity: 0.6;
  }
}

.btn-quick-chip {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  color: #334155;

  &:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
    transform: translateY(-1px);
  }
}

.transfer-arrow-badge {
  width: 32px;
  height: 32px;
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

  .input-group-text {
    border: none;
  }

  .form-control, .form-select {
    border: none;
    box-shadow: none;
    font-size: 0.95rem;

    &:focus {
      box-shadow: none;
    }
  }
}

.btn-date-preset {
  cursor: pointer;
  &:hover {
    opacity: 0.9;
  }
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
