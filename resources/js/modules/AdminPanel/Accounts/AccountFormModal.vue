<template>
  <div v-if="modelValue" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3" @click.self="close">
    <div class="modal-card bg-white rounded-4 shadow-xl border-0 overflow-hidden animate-scale-in" style="max-width: 520px; width: 100%;">
      
      <!-- Modal Header -->
      <div class="modal-header-custom d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
          <div class="header-icon-pill rounded-3 p-2 d-flex align-items-center justify-content-center" :class="selectedTypeClass">
            <component :is="selectedTypeIcon" :size="20" />
          </div>
          <div>
            <h5 class="fw-bold mb-0 text-dark">{{ editData ? 'Edit Account' : 'Add New Account' }}</h5>
            <p class="text-muted small mb-0">Track cash, bank balance, mobile money, or cards</p>
          </div>
        </div>
        <button type="button" class="btn-close-custom btn btn-icon btn-light rounded-circle p-2" @click="close" aria-label="Close">
          <X :size="18" class="text-muted" />
        </button>
      </div>

      <!-- Modal Body Form -->
      <form @submit.prevent="handleSubmit" class="p-4">
        
        <!-- 1. Account Type Selector (Visual Cards) -->
        <div class="mb-4">
          <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-2 d-flex align-items-center justify-content-between">
            <span>Account Type <span class="text-danger">*</span></span>
            <span class="badge bg-light text-secondary border fw-normal">{{ selectedTypeName }}</span>
          </label>
          <div class="row g-2">
            <div v-for="type in accountTypes" :key="type.id" class="col-6 col-sm-4 col-md">
              <button
                type="button"
                class="account-type-btn w-100 p-2.5 rounded-3 d-flex flex-column align-items-center text-center transition-all"
                :class="{ 'active-type': form.account_type === type.id }"
                @click="selectAccountType(type.id)"
              >
                <div class="type-icon-wrapper rounded-circle p-2 mb-1.5" :class="type.iconClass">
                  <component :is="type.icon" :size="18" />
                </div>
                <span class="fw-semibold text-xs text-dark lh-1">{{ type.label }}</span>
                <small class="text-muted text-xxs mt-0.5">{{ type.hint }}</small>
              </button>
            </div>
          </div>
        </div>

        <!-- Smart Quick Name Suggestions -->
        <div v-if="suggestedNames.length > 0 && !editData" class="mb-3">
          <div class="d-flex align-items-center gap-1.5 flex-wrap">
            <span class="text-xxs text-muted fw-medium me-1">Popular:</span>
            <button
              v-for="sug in suggestedNames"
              :key="sug"
              type="button"
              class="badge bg-light text-dark border-0 hover-badge rounded-pill px-2.5 py-1 text-xxs fw-medium transition-all"
              @click="applySuggestedName(sug)"
            >
              + {{ sug }}
            </button>
          </div>
        </div>

        <!-- 2. Account Name Input -->
        <div class="mb-3">
          <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
            Account Name <span class="text-danger">*</span>
          </label>
          <div class="input-group input-group-modern">
            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
              <PencilLine :size="16" />
            </span>
            <input
              v-model="form.name"
              type="text"
              class="form-control form-control-modern border-start-0 ps-2"
              :placeholder="namePlaceholder"
              required
              autofocus
            />
          </div>
        </div>

        <!-- 3. Starting Balance & Currency Group -->
        <div class="row g-3 mb-3">
          <div class="col-7">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
              Starting Balance
            </label>
            <div class="input-group input-group-modern">
              <span class="input-group-text bg-light border-end-0 fw-bold text-success ps-3">
                ৳
              </span>
              <input
                v-model.number="form.balance"
                type="number"
                step="0.01"
                class="form-control form-control-modern border-start-0 ps-2 fw-semibold"
                placeholder="0.00"
              />
            </div>
            <div class="form-text text-xxs text-muted mt-1">Initial funds currently in this account</div>
          </div>

          <div class="col-5">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
              Currency
            </label>
            <div class="currency-pill d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border">
              <div class="d-flex align-items-center gap-1.5">
                <span class="flag-icon">🇧🇩</span>
                <span class="fw-bold text-xs text-dark">BDT (৳)</span>
              </div>
              <span class="badge bg-success-subtle text-success text-xxs fw-semibold">Default</span>
            </div>
          </div>
        </div>

        <!-- 4. Collapsible "More options (optional)" -->
        <div class="optional-section mt-3 pt-2">
          <button
            type="button"
            class="btn btn-link text-decoration-none p-0 text-muted d-flex align-items-center gap-1.5 text-xs fw-semibold hover-text-primary"
            @click="showMoreOptions = !showMoreOptions"
          >
            <ChevronDown :size="15" class="transition-transform" :class="{ 'rotate-180': showMoreOptions }" />
            <span>{{ showMoreOptions ? 'Hide extra details' : 'More options (optional)' }}</span>
          </button>

          <div v-show="showMoreOptions" class="optional-content mt-3 p-3 bg-light-subtle rounded-3 border border-dashed animate-fade-in">
            <!-- Account Number -->
            <div class="mb-3">
              <label class="form-label text-xs fw-semibold text-secondary mb-1">
                Account Number / Card Digits (Optional)
              </label>
              <div class="input-group input-group-modern input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                  <Hash :size="14" />
                </span>
                <input
                  v-model="form.account_number"
                  type="text"
                  class="form-control form-control-sm border-start-0"
                  placeholder="XXXX-XXXX-XXXX"
                />
              </div>
              <div class="form-text text-xxs text-muted mt-1">Only for your own reference (last 4 digits or A/C)</div>
            </div>

            <!-- Description / Note -->
            <div>
              <label class="form-label text-xs fw-semibold text-secondary mb-1">
                Notes / Purpose
              </label>
              <textarea
                v-model="form.description"
                class="form-control form-control-sm"
                rows="2"
                placeholder="e.g. Primary salary account at Gulshan branch"
              ></textarea>
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
            class="btn btn-primary rounded-pill px-4 py-2 text-sm fw-semibold shadow-sm d-flex align-items-center gap-2"
            :disabled="saving"
          >
            <span v-if="saving" class="spinner-border spinner-border-sm" role="status"></span>
            <Check v-else :size="16" />
            <span>{{ saving ? 'Saving...' : (editData ? 'Save Changes' : 'Save Account') }}</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import {
  Wallet,
  Building2,
  Smartphone,
  CreditCard,
  Layers,
  X,
  Check,
  ChevronDown,
  PencilLine,
  Hash,
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
  saving: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue', 'save']);

const showMoreOptions = ref(false);

const form = ref({
  name: '',
  account_type: 'cash',
  balance: 0,
  currency: 'BDT',
  account_number: '',
  description: '',
});

const accountTypes = [
  { id: 'cash', label: 'Cash', hint: 'In-hand', icon: Wallet, iconClass: 'bg-success-subtle text-success' },
  { id: 'bank', label: 'Bank', hint: 'Checking/Savings', icon: Building2, iconClass: 'bg-primary-subtle text-primary' },
  { id: 'mobile', label: 'Mobile Wallet', hint: 'bKash/Nagad', icon: Smartphone, iconClass: 'bg-info-subtle text-info' },
  { id: 'credit_card', label: 'Credit Card', hint: 'Card limit', icon: CreditCard, iconClass: 'bg-warning-subtle text-warning' },
  { id: 'other', label: 'Other', hint: 'Investment', icon: Layers, iconClass: 'bg-secondary-subtle text-secondary' },
];

const selectedType = computed(() => accountTypes.find(t => t.id === form.value.account_type) || accountTypes[0]);
const selectedTypeName = computed(() => selectedType.value.label);
const selectedTypeIcon = computed(() => selectedType.value.icon);
const selectedTypeClass = computed(() => selectedType.value.iconClass);

const namePlaceholder = computed(() => {
  switch (form.value.account_type) {
    case 'cash': return 'e.g. Daily Cash, Pocket Money';
    case 'mobile': return 'e.g. bKash Personal, Nagad Main';
    case 'bank': return 'e.g. City Bank Salary, BRAC Bank';
    case 'credit_card': return 'e.g. SCB Platinum Card, EBL Visa';
    default: return 'e.g. Investment Portfolio';
  }
});

const suggestedNames = computed(() => {
  switch (form.value.account_type) {
    case 'mobile': return ['bKash Personal', 'Nagad', 'Rocket', 'Upay'];
    case 'bank': return ['City Bank Salary', 'BRAC Bank', 'Dutch-Bangla Bank', 'Eastern Bank', 'Islami Bank'];
    case 'cash': return ['Daily Cash', 'Emergency Cash', 'Wallet'];
    case 'credit_card': return ['Primary Credit Card', 'Travel Card'];
    default: return [];
  }
});

function selectAccountType(typeId) {
  form.value.account_type = typeId;
  if (!form.value.name || suggestedNames.value.includes(form.value.name)) {
    if (typeId === 'cash') form.value.name = 'Cash';
  }
}

function applySuggestedName(name) {
  form.value.name = name;
}

watch(
  () => props.editData,
  (val) => {
    if (val) {
      form.value = {
        name: val.name || '',
        account_type: val.account_type || 'cash',
        balance: val.balance ?? 0,
        currency: val.currency || 'BDT',
        account_number: val.account_number || '',
        description: val.description || '',
      };
      if (val.account_number || val.description) {
        showMoreOptions.value = true;
      }
    } else {
      form.value = {
        name: 'Cash',
        account_type: 'cash',
        balance: 0,
        currency: 'BDT',
        account_number: '',
        description: '',
      };
      showMoreOptions.value = false;
    }
  },
  { immediate: true }
);

function close() {
  emit('update:modelValue', false);
}

function handleSubmit() {
  emit('save', { ...form.value });
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

.account-type-btn {
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  cursor: pointer;
  height: 100%;

  &:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
    transform: translateY(-1px);
  }

  &.active-type {
    border-color: #137035;
    background: #f0fdf4;
    box-shadow: 0 0 0 1px #137035;
  }
}

.type-icon-wrapper {
  display: inline-flex;
  align-items: center;
  justify-content: center;
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

  .form-control {
    border: none;
    box-shadow: none;
    font-size: 0.95rem;

    &:focus {
      box-shadow: none;
    }
  }
}

.currency-pill {
  height: 44px;
}

.hover-badge {
  cursor: pointer;
  &:hover {
    background-color: #e2e8f0 !important;
    color: #0f172a !important;
  }
}

.hover-text-primary:hover {
  color: #137035 !important;
}

.rotate-180 {
  transform: rotate(180deg);
}

.text-xs { font-size: 0.75rem; }
.text-xxs { font-size: 0.6875rem; }
.tracking-wider { letter-spacing: 0.05em; }

.animate-scale-in {
  animation: scaleIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.animate-fade-in {
  animation: fadeIn 0.2s ease;
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

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>
