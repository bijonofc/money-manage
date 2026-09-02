<template>
  <div class="design-showcase-page pb-5">
    
    <!-- Hero Header Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 showcase-hero-banner overflow-hidden position-relative">
      <div class="card-body p-4 p-md-5 text-white position-relative z-1">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
          <div style="max-width: 650px;">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-20 text-white text-xs fw-semibold mb-3 backdrop-blur">
              <span>🇧🇩 Bangladeshi Taka (৳) Optimized</span>
              <span>•</span>
              <span>Fintech UX Redesign</span>
            </div>
            <h2 class="fw-bolder mb-2 text-white display-6">
              Personal Finance UX Redesign
            </h2>
            <p class="text-white-50 mb-0 fs-6">
              Transformed complex ERP-style accounting forms into an effortless, friction-free personal money manager. Record transactions and set up accounts in seconds.
            </p>
          </div>

          <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-light rounded-pill px-4 py-2.5 fw-bold text-dark shadow-sm d-flex align-items-center gap-2" @click="showAccountModal = true">
              <Plus :size="18" class="text-success" />
              <span>Test "Add Account" Modal</span>
            </button>
            <button class="btn btn-warning rounded-pill px-4 py-2.5 fw-bold text-dark shadow-sm d-flex align-items-center gap-2" @click="showTransactionModal = true">
              <Zap :size="18" class="text-dark" />
              <span>Test "Record Transaction" Modal</span>
            </button>
          </div>
        </div>
      </div>
      <div class="showcase-decor-circle-1"></div>
      <div class="showcase-decor-circle-2"></div>
    </div>

    <!-- UX Improvements Key Highlights Bar -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
          <div class="d-flex align-items-center gap-3">
            <div class="p-2.5 rounded-3 bg-success-subtle text-success">
              <Coins :size="22" />
            </div>
            <div>
              <h6 class="fw-bold mb-0 text-dark">Hero Amount First</h6>
              <small class="text-muted">Prominent ৳ typography with 1-tap quick Taka increment chips.</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
          <div class="d-flex align-items-center gap-3">
            <div class="p-2.5 rounded-3 bg-primary-subtle text-primary">
              <ArrowLeftRight :size="22" />
            </div>
            <div>
              <h6 class="fw-bold mb-0 text-dark">Contextual Terminology</h6>
              <small class="text-muted">"From Account" for Expense, "To Account" for Income, paired flow for Transfers.</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
          <div class="d-flex align-items-center gap-3">
            <div class="p-2.5 rounded-3 bg-info-subtle text-info">
              <Smartphone :size="22" />
            </div>
            <div>
              <h6 class="fw-bold mb-0 text-dark">Bangladeshi Local Presets</h6>
              <small class="text-muted">Direct visual support for bKash, Nagad, Rocket, and Bangladeshi Banks.</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- SIDE-BY-SIDE INTERACTIVE FORMS SHOWCASE -->
    <div class="row g-4">
      
      <!-- SCREEN 1: ADD ACCOUNT FORM CARD -->
      <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-white">
          <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-success text-white rounded-pill px-2.5 py-1 text-xs fw-bold">Screen 1</span>
              <h5 class="fw-bold mb-0 text-dark">Add Account</h5>
            </div>
            <span class="badge bg-light text-secondary border">Interactive Preview</span>
          </div>

          <div class="card-body p-4">
            
            <!-- Account Type Selector (Visual Cards) -->
            <div class="mb-4">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-2 d-flex align-items-center justify-content-between">
                <span>Account Type <span class="text-danger">*</span></span>
                <span class="badge bg-success-subtle text-success border-0 text-capitalize">{{ inlineAccount.account_type.replace('_', ' ') }}</span>
              </label>
              <div class="row g-2">
                <div v-for="type in accountTypes" :key="type.id" class="col-6 col-sm-4 col-md">
                  <button
                    type="button"
                    class="account-type-btn w-100 p-2.5 rounded-3 d-flex flex-column align-items-center text-center transition-all"
                    :class="{ 'active-type': inlineAccount.account_type === type.id }"
                    @click="inlineAccount.account_type = type.id"
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

            <!-- Smart Quick Suggestions -->
            <div class="mb-3">
              <div class="d-flex align-items-center gap-1.5 flex-wrap">
                <span class="text-xxs text-muted fw-medium me-1">Quick Name:</span>
                <button
                  v-for="sug in suggestedNamesForType"
                  :key="sug"
                  type="button"
                  class="badge bg-light text-dark border-0 hover-badge rounded-pill px-2.5 py-1 text-xxs fw-medium transition-all"
                  @click="inlineAccount.name = sug"
                >
                  + {{ sug }}
                </button>
              </div>
            </div>

            <!-- Account Name -->
            <div class="mb-3">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
                Account Name <span class="text-danger">*</span>
              </label>
              <div class="input-group input-group-modern">
                <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                  <PencilLine :size="16" />
                </span>
                <input
                  v-model="inlineAccount.name"
                  type="text"
                  class="form-control form-control-modern border-start-0 ps-2"
                  placeholder="e.g. Daily Cash, City Bank, bKash Personal"
                />
              </div>
            </div>

            <!-- Starting Balance & Currency Group -->
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
                    v-model.number="inlineAccount.balance"
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

            <!-- Collapsed More Options -->
            <div class="optional-section mt-3 pt-2">
              <button
                type="button"
                class="btn btn-link text-decoration-none p-0 text-muted d-flex align-items-center gap-1.5 text-xs fw-semibold hover-text-primary"
                @click="inlineAccountShowMore = !inlineAccountShowMore"
              >
                <ChevronDown :size="15" class="transition-transform" :class="{ 'rotate-180': inlineAccountShowMore }" />
                <span>{{ inlineAccountShowMore ? 'Hide extra details' : 'More options (optional)' }}</span>
              </button>

              <div v-show="inlineAccountShowMore" class="optional-content mt-3 p-3 bg-light-subtle rounded-3 border border-dashed animate-fade-in">
                <div class="mb-3">
                  <label class="form-label text-xs fw-semibold text-secondary mb-1">
                    Account Number / Card Digits (Optional)
                  </label>
                  <div class="input-group input-group-modern input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                      <Hash :size="14" />
                    </span>
                    <input
                      v-model="inlineAccount.account_number"
                      type="text"
                      class="form-control form-control-sm border-start-0"
                      placeholder="XXXX-XXXX-XXXX"
                    />
                  </div>
                </div>

                <div>
                  <label class="form-label text-xs fw-semibold text-secondary mb-1">
                    Notes / Description
                  </label>
                  <textarea
                    v-model="inlineAccount.description"
                    class="form-control form-control-sm"
                    rows="2"
                    placeholder="e.g. Primary salary account at Gulshan branch"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2.5 mt-4 pt-3 border-top">
              <button type="button" class="btn btn-light rounded-pill px-4 py-2 text-sm fw-semibold text-secondary">
                Cancel
              </button>
              <button
                type="button"
                class="btn btn-primary rounded-pill px-4 py-2 text-sm fw-semibold shadow-sm d-flex align-items-center gap-2"
                @click="simulateSave('Account saved successfully!')"
              >
                <Check :size="16" />
                <span>Save Account</span>
              </button>
            </div>

          </div>
        </div>
      </div>

      <!-- SCREEN 2: RECORD TRANSACTION FORM CARD -->
      <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-white">
          <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 text-xs fw-bold">Screen 2</span>
              <h5 class="fw-bold mb-0 text-dark">Record Transaction</h5>
            </div>
            <span class="badge bg-light text-secondary border">Interactive Preview</span>
          </div>

          <div class="card-body p-4">
            
            <!-- Segmented Control Tabs -->
            <div class="segmented-control p-1 rounded-3 bg-light d-flex align-items-center gap-1 mb-4">
              <button
                type="button"
                class="segmented-tab flex-fill py-2 px-3 rounded-3 d-flex align-items-center justify-content-center gap-2 transition-all fw-bold text-xs"
                :class="{ 'tab-expense-active': inlineTx.transaction_type === 'expense' }"
                @click="inlineTx.transaction_type = 'expense'"
              >
                <ArrowUpRight :size="16" />
                <span>EXPENSE</span>
              </button>

              <button
                type="button"
                class="segmented-tab flex-fill py-2 px-3 rounded-3 d-flex align-items-center justify-content-center gap-2 transition-all fw-bold text-xs"
                :class="{ 'tab-income-active': inlineTx.transaction_type === 'income' }"
                @click="inlineTx.transaction_type = 'income'"
              >
                <ArrowDownLeft :size="16" />
                <span>INCOME</span>
              </button>

              <button
                type="button"
                class="segmented-tab flex-fill py-2 px-3 rounded-3 d-flex align-items-center justify-content-center gap-2 transition-all fw-bold text-xs"
                :class="{ 'tab-transfer-active': inlineTx.transaction_type === 'transfer' }"
                @click="inlineTx.transaction_type = 'transfer'"
              >
                <ArrowLeftRight :size="16" />
                <span>TRANSFER</span>
              </button>
            </div>

            <!-- Hero Amount Section -->
            <div class="hero-amount-card p-3 rounded-4 mb-4 border text-center transition-all" :class="'hero-theme-' + inlineTx.transaction_type">
              <label class="text-xs fw-bold text-uppercase tracking-wider opacity-75 mb-1 d-block">
                {{ inlineTx.transaction_type === 'expense' ? 'Expense Amount' : inlineTx.transaction_type === 'income' ? 'Income Amount' : 'Transfer Amount' }} <span class="text-danger">*</span>
              </label>
              
              <div class="amount-input-wrapper d-inline-flex align-items-baseline justify-content-center">
                <span class="currency-symbol display-6 fw-bold me-1 text-dark opacity-75">৳</span>
                <input
                  v-model="inlineTx.amount"
                  type="text"
                  inputmode="decimal"
                  class="hero-amount-input fw-bolder text-center"
                  placeholder="0.00"
                />
              </div>

              <!-- Quick Taka Chips -->
              <div class="quick-chips d-flex align-items-center justify-content-center gap-1.5 flex-wrap mt-2.5">
                <button
                  v-for="chip in [100, 500, 1000, 2000, 5000]"
                  :key="chip"
                  type="button"
                  class="btn btn-sm btn-quick-chip rounded-pill px-2.5 py-1 text-xxs fw-semibold transition-all"
                  @click="addInlineTxAmount(chip)"
                >
                  +৳{{ chip.toLocaleString() }}
                </button>
                <button
                  v-if="parseFloat(inlineTx.amount) > 0"
                  type="button"
                  class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0.5 text-xxs fw-normal"
                  @click="inlineTx.amount = ''"
                >
                  Clear
                </button>
              </div>
            </div>

            <!-- Dynamic Field Layout -->
            <!-- Dynamic Field Layout -->
            <div v-if="inlineTx.transaction_type !== 'transfer'">
              <div class="mb-3">
                <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
                  {{ inlineTx.transaction_type === 'expense' ? 'From Account' : 'To Account' }} <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-modern">
                  <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                    <Wallet :size="16" />
                  </span>
                  <select v-model="inlineTx.account_id" class="form-select form-control-modern border-start-0 ps-2">
                    <option v-for="acc in sampleAccounts" :key="acc.id" :value="acc.id">
                      {{ acc.name }} (৳{{ acc.balance.toLocaleString() }})
                    </option>
                  </select>
                </div>
              </div>

              <!-- Visual Category Picker in Showcase -->
              <div class="mb-3">
                <CategoryPicker
                  v-model="inlineTx.category_id"
                  :type="inlineTx.transaction_type"
                  :categories="[...sampleExpenseCategories, ...sampleIncomeCategories]"
                />
              </div>
            </div>

            <!-- Transfer Layout (From -> To) -->
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
                    <select v-model="inlineTx.from_account_id" class="form-select form-control-sm border-start-0">
                      <option v-for="acc in sampleAccounts" :key="'from-' + acc.id" :value="acc.id">
                        {{ acc.name }} (৳{{ acc.balance.toLocaleString() }})
                      </option>
                    </select>
                  </div>
                </div>

                <div class="col-12 col-md-2 text-center py-1">
                  <div class="transfer-arrow-badge mx-auto rounded-circle d-flex align-items-center justify-content-center text-primary bg-white shadow-sm border">
                    <ArrowRight :size="16" />
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
                    <select v-model="inlineTx.account_id" class="form-select form-control-sm border-start-0">
                      <option v-for="acc in sampleAccounts" :key="'to-' + acc.id" :value="acc.id" :disabled="acc.id === inlineTx.from_account_id">
                        {{ acc.name }} (৳{{ acc.balance.toLocaleString() }})
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Date and Time -->
            <div class="row g-3 mb-3">
              <div class="col-12 col-sm-7">
                <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5 d-flex align-items-center justify-content-between">
                  <span>Date <span class="text-danger">*</span></span>
                  <span class="badge bg-light text-muted text-xxs">Today · Sep 2, 2026</span>
                </label>
                <div class="input-group input-group-modern">
                  <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                    <Calendar :size="16" />
                  </span>
                  <input v-model="inlineTx.date" type="date" class="form-control form-control-modern border-start-0 ps-2" />
                </div>
              </div>

              <div class="col-12 col-sm-5">
                <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
                  Time
                </label>
                <div class="input-group input-group-modern">
                  <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                    <Clock :size="16" />
                  </span>
                  <input v-model="inlineTx.time" type="time" class="form-control form-control-modern border-start-0 ps-2" />
                </div>
              </div>
            </div>

            <!-- Note / Description -->
            <div class="mb-3">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
                Note (Optional)
              </label>
              <div class="input-group input-group-modern">
                <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                  <FileText :size="16" />
                </span>
                <input
                  v-model="inlineTx.description"
                  type="text"
                  class="form-control form-control-modern border-start-0 ps-2"
                  placeholder="What was this for?"
                />
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2.5 mt-4 pt-3 border-top">
              <button type="button" class="btn btn-light rounded-pill px-4 py-2 text-sm fw-semibold text-secondary">
                Cancel
              </button>
              <button
                type="button"
                class="btn rounded-pill px-4 py-2 text-sm fw-semibold shadow-sm d-flex align-items-center gap-2"
                :class="inlineTx.transaction_type === 'expense' ? 'btn-danger text-white' : 'btn-primary text-white'"
                @click="simulateSave('Transaction recorded successfully!')"
              >
                <Check :size="16" />
                <span>
                  {{ inlineTx.transaction_type === 'expense' ? 'Save Expense' : inlineTx.transaction_type === 'income' ? 'Save Income' : 'Save Transfer' }}
                </span>
              </button>
            </div>

          </div>
        </div>
      </div>

    </div>

    <!-- Modals for Full Interactive Testing -->
    <AccountFormModal
      v-model="showAccountModal"
      :saving="modalSaving"
      @save="onSaveAccountModal"
    />

    <TransactionFormModal
      v-model="showTransactionModal"
      :account-list="sampleAccounts"
      :category-list="[...sampleExpenseCategories, ...sampleIncomeCategories]"
      :saving="modalSaving"
      @save="onSaveTransactionModal"
    />

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import AccountFormModal from '@/modules/AdminPanel/Accounts/AccountFormModal.vue';
import TransactionFormModal from '@/modules/AdminPanel/Transactions/TransactionFormModal.vue';
import CategoryPicker from '@/components/CategoryPicker.vue';
import AppsbdUtls from '@/libs/AppsbdUtls.js';

import {
  Plus,
  Zap,
  Coins,
  ArrowLeftRight,
  ArrowUpRight,
  ArrowDownLeft,
  ArrowRight,
  Wallet,
  Building2,
  Smartphone,
  CreditCard,
  Layers,
  PencilLine,
  Tag,
  Calendar,
  Clock,
  FileText,
  Check,
  ChevronDown,
  Hash,
} from '@lucide/vue';

const showAccountModal = ref(false);
const showTransactionModal = ref(false);
const modalSaving = ref(false);

const accountTypes = [
  { id: 'cash', label: 'Cash', hint: 'In-hand', icon: Wallet, iconClass: 'bg-success-subtle text-success' },
  { id: 'bank', label: 'Bank', hint: 'Checking/Savings', icon: Building2, iconClass: 'bg-primary-subtle text-primary' },
  { id: 'mobile', label: 'Mobile Wallet', hint: 'bKash/Nagad', icon: Smartphone, iconClass: 'bg-info-subtle text-info' },
  { id: 'credit_card', label: 'Credit Card', hint: 'Card limit', icon: CreditCard, iconClass: 'bg-warning-subtle text-warning' },
  { id: 'other', label: 'Other', hint: 'Investment', icon: Layers, iconClass: 'bg-secondary-subtle text-secondary' },
];

const sampleAccounts = ref([
  { id: 1, name: '💵 Cash in Hand', balance: 4750.00, account_type: 'cash' },
  { id: 2, name: '🏦 City Bank Salary', balance: 85200.00, account_type: 'bank' },
  { id: 3, name: '📱 bKash Personal', balance: 3450.00, account_type: 'mobile' },
  { id: 4, name: '💳 SCB Platinum Card', balance: -12500.00, account_type: 'credit_card' },
]);

const sampleExpenseCategories = ref([
  { id: 1, name: 'Food & Dining', icon: '🍔', type: 'expense' },
  { id: 2, name: 'Groceries & Bazar', icon: '🛒', type: 'expense' },
  { id: 3, name: 'Transport & Uber', icon: '🚗', type: 'expense' },
  { id: 4, name: 'Bills & Utilities', icon: '💡', type: 'expense' },
  { id: 5, name: 'Shopping', icon: '🛍️', type: 'expense' },
]);

const sampleIncomeCategories = ref([
  { id: 6, name: 'Monthly Salary', icon: '💼', type: 'income' },
  { id: 7, name: 'Freelance & Projects', icon: '💻', type: 'income' },
  { id: 8, name: 'Investments & Returns', icon: '📈', type: 'income' },
  { id: 9, name: 'Gift / Allowance', icon: '🎁', type: 'income' },
]);

// Inline Account State
const inlineAccount = ref({
  name: 'Cash in Hand',
  account_type: 'cash',
  balance: 4750,
  currency: 'BDT',
  account_number: '',
  description: '',
});
const inlineAccountShowMore = ref(false);

const suggestedNamesForType = computed(() => {
  switch (inlineAccount.value.account_type) {
    case 'mobile': return ['bKash Personal', 'Nagad', 'Rocket', 'Upay'];
    case 'bank': return ['City Bank Salary', 'BRAC Bank', 'Dutch-Bangla Bank', 'Eastern Bank'];
    case 'cash': return ['Daily Cash', 'Emergency Cash', 'Wallet'];
    case 'credit_card': return ['Primary Credit Card', 'Travel Card'];
    default: return ['Portfolio', 'Locker'];
  }
});

// Inline Transaction State
const inlineTx = ref({
  transaction_type: 'expense',
  amount: '250.00',
  account_id: 1,
  from_account_id: 2,
  category_id: 1,
  date: new Date().toISOString().split('T')[0],
  description: 'Lunch at Dhanmondi',
});

function addInlineTxAmount(val) {
  const cur = parseFloat(inlineTx.value.amount) || 0;
  inlineTx.value.amount = (cur + val).toString();
}

function simulateSave(msg) {
  AppsbdUtls.ShowServerResponseNotification(msg, 3000);
}

function onSaveAccountModal(data) {
  modalSaving.value = true;
  setTimeout(() => {
    modalSaving.value = false;
    showAccountModal.value = false;
    AppsbdUtls.ShowServerResponseNotification('Account "' + data.name + '" created successfully!', 3000);
  }, 600);
}

function onSaveTransactionModal(data) {
  modalSaving.value = true;
  setTimeout(() => {
    modalSaving.value = false;
    showTransactionModal.value = false;
    AppsbdUtls.ShowServerResponseNotification('Transaction of ৳' + data.amount + ' recorded!', 3000);
  }, 600);
}
</script>

<style scoped lang="scss">
.showcase-hero-banner {
  background: linear-gradient(135deg, #137035 0%, #0d5226 50%, #064e3b 100%);
}

.backdrop-blur {
  backdrop-filter: blur(8px);
}

.showcase-decor-circle-1 {
  position: absolute;
  top: -80px;
  right: -80px;
  width: 260px;
  height: 260px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  pointer-events: none;
}

.showcase-decor-circle-2 {
  position: absolute;
  bottom: -60px;
  left: 20%;
  width: 180px;
  height: 180px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.05);
  pointer-events: none;
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

  .form-control, .form-select {
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

.text-xs { font-size: 0.75rem; }
.text-xxs { font-size: 0.6875rem; }
.tracking-wider { letter-spacing: 0.05em; }

.animate-fade-in {
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>
