<template>
  <div class="transactions-page pb-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
          <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
            <ArrowLeftRight :size="24" class="text-primary" />
            Transactions
          </h4>
          <p class="text-muted small mb-0">Record and track income, expenses, and account transfers</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <ab-button color="secondary" is-outline :is-animated="isShowLoader" :disabled="isShowLoader" @click="loadGridData">
            Refresh
          </ab-button>
          <ab-button color="primary" @click="openCreateModal">
            Add Transaction
          </ab-button>
        </div>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-3 p-3">
      <div class="row g-2 align-items-center">
        <div class="col-12 col-md-3">
          <select v-model="filterType" class="form-select form-select-sm" @change="onFilterChange">
            <option value="">All Types</option>
            <option value="income">Income</option>
            <option value="expense">Expense</option>
            <option value="transfer">Transfer</option>
          </select>
        </div>
        <div class="col-12 col-md-3">
          <select v-model="filterAccount" class="form-select form-select-sm" @change="onFilterChange">
            <option value="">All Accounts</option>
            <option v-for="acc in accountList" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- EliteGrid Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white p-3">
      <div class="elite-grid-container">
        <elite-grid
          :is-rounded="true"
          :is-group-separate-head="true"
          action-width="90px"
          :columns="data_column"
          :show-loader="isShowLoader"
          :show-header="false"
          :grid-data="gridData"
          :show-action-column="true"
          :is-show-row-index-column="true"
          @load-data="eliteGridLoadData"
        >
          <!-- Loader Slot -->
          <template #slot-loader>
            <APBDGridLoader msg="Loading transactions..." />
          </template>

          <!-- Empty Slot -->
          <template #slot-no-record>
            <div class="p-5 text-center text-muted">
              <ArrowLeftRight :size="48" class="mx-auto mb-3 opacity-50" />
              <h5>No transactions found</h5>
              <p class="small mb-4">Record your first transaction to keep your finances accurate.</p>
              <ab-button color="primary" @click="openCreateModal">
                Add Transaction
              </ab-button>
            </div>
          </template>

          <!-- Description & Category Slot -->
          <template #slotdescription="{ rowitem }">
            <div class="d-flex align-items-center gap-2.5 py-1">
              <div
                class="tx-icon-pill rounded-circle p-2 d-flex align-items-center justify-content-center"
                :class="{
                  'bg-success-subtle text-success': rowitem.transaction_type === 'income',
                  'bg-danger-subtle text-danger': rowitem.transaction_type === 'expense',
                  'bg-primary-subtle text-primary': rowitem.transaction_type === 'transfer'
                }"
              >
                <ArrowDownLeft v-if="rowitem.transaction_type === 'income'" :size="15" />
                <ArrowUpRight v-else-if="rowitem.transaction_type === 'expense'" :size="15" />
                <ArrowLeftRight v-else :size="15" />
              </div>
              <div>
                <div class="fw-semibold text-dark text-xs">{{ rowitem.description || rowitem.category_name || 'Transaction' }}</div>
                <small class="text-muted text-xxs">{{ rowitem.category_name || 'Uncategorized' }}</small>
              </div>
            </div>
          </template>

          <!-- Type Slot -->
          <template #slottransaction_type="{ rowitem }">
            <span
              class="badge text-capitalize text-xxs px-2 py-1 fw-semibold"
              :class="{
                'bg-success-subtle text-success border border-success-subtle': rowitem.transaction_type === 'income',
                'bg-danger-subtle text-danger border border-danger-subtle': rowitem.transaction_type === 'expense',
                'bg-primary-subtle text-primary border border-primary-subtle': rowitem.transaction_type === 'transfer'
              }"
            >
              {{ rowitem.transaction_type }}
            </span>
          </template>

          <!-- Account Slot -->
          <template #slotaccount_name="{ rowitem }">
            <span class="badge bg-light text-dark border text-xxs px-2 py-1">
              {{ rowitem.account_name || 'Account' }}
            </span>
          </template>

          <!-- Date Slot -->
          <template #slotdate="{ rowitem }">
            <span class="text-xs text-secondary">{{ rowitem.date }}</span>
          </template>

          <!-- Amount Slot -->
          <template #slotamount="{ rowitem }">
            <span
              class="fw-bold text-xs text-nowrap"
              :class="{
                'text-success': rowitem.transaction_type === 'income',
                'text-danger': rowitem.transaction_type === 'expense',
                'text-primary': rowitem.transaction_type === 'transfer'
              }"
            >
              {{ rowitem.transaction_type === 'income' ? '+' : rowitem.transaction_type === 'expense' ? '-' : '' }}{{ currencySymbol }}{{ formatNumber(rowitem.amount) }}
            </span>
          </template>

          <!-- Action Buttons Slot -->
          <template #slot-action-buttons="{ rowitem }">
            <button
              class="btn btn-icon btn-light btn-sm rounded-circle text-danger"
              title="Delete"
              @click="deleteTransaction(rowitem.id)"
            >
              <Trash2 :size="14" />
            </button>
          </template>
        </elite-grid>
      </div>
    </div>

    <!-- Record Transaction Modal -->
    <TransactionFormModal
      v-model="showModal"
      :account-list="accountList"
      :category-list="categoryList"
      :saving="saving"
      @save="saveTransaction"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js';
import AppsbdURL from '@/libs/AppsbdURL.js';
import AppsbdUtls from '@/libs/AppsbdUtls.js';
import APBDRequestParam from '@/libs/APBDRequestParam.js';
import APBDGridLoader from '@/components/APBDGridLoader.vue';
import EliteGrid, { EliteColumnModel } from '@appsbd/vue3-elite-grid';
import TransactionFormModal from './TransactionFormModal.vue';

import {
  ArrowLeftRight,
  ArrowUpRight,
  ArrowDownLeft,
  Trash2,
} from '@lucide/vue';

const isShowLoader = ref(false);
const saving = ref(false);
const showModal = ref(false);

const accountList = ref([]);
const categoryList = ref([]);
const filterType = ref('');
const filterAccount = ref('');

const currencySymbol = computed(() => window.app_settings?.currencySymbol || '৳');

const gridData = reactive({
  page: 1,
  total: 1,
  records: 0,
  limit: 20,
  rowdata: [],
});

const sortProps = ref(null);

const data_column = [
  EliteColumnModel.getColumn({ name: 'description', title: 'tx.description', width: '260px', is_sortable: false }),
  EliteColumnModel.getColumn({ name: 'transaction_type', title: 'tx.type', width: '120px', is_sortable: true }),
  EliteColumnModel.getColumn({ name: 'account_name', title: 'tx.account', width: '160px', is_sortable: false }),
  EliteColumnModel.getColumn({ name: 'date', title: 'tx.date', width: '130px', is_sortable: true }),
  EliteColumnModel.getColumn({ name: 'amount', title: 'tx.amount', width: '140px', is_sortable: true }),
];

function formatNumber(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

async function loadGridData() {
  isShowLoader.value = true;
  const param = new APBDRequestParam();
  param.limit = gridData.limit;
  param.page = gridData.page;

  if (filterType.value) {
    param.AddSrcItem('transaction_type', filterType.value, 'eq');
  }
  if (filterAccount.value) {
    param.AddSrcItem('account_id', filterAccount.value, 'eq');
  }

  if (sortProps.value) {
    param.AddSortItem(sortProps.value.prop, sortProps.value.ord);
  }

  try {
    const res = await AxiosHelper.post(AppsbdURL.route('transactions/list'), param);
    if (res?.data) {
      gridData.page = res.data.page || 1;
      gridData.limit = res.data.limit || 20;
      gridData.records = res.data.records || 0;
      gridData.total = res.data.total || 1;
      gridData.rowdata = res.data.rowdata || [];
    }
  } catch (e) {
    console.error('Failed to load transaction data', e);
  } finally {
    isShowLoader.value = false;
  }
}

function eliteGridLoadData(e) {
  gridData.page = e.page;
  gridData.limit = e.limit;
  if (e.sort_prop) {
    sortProps.value = { prop: e.sort_prop, ord: e.sort_ord };
  } else {
    sortProps.value = null;
  }
  loadGridData();
}

function onFilterChange() {
  gridData.page = 1;
  loadGridData();
}

async function loadDependencies() {
  try {
    const accRes = await AxiosHelper.post(AppsbdURL.route('accounts/list'), {});
    if (accRes?.data?.rowdata) {
      accountList.value = accRes.data.rowdata;
    }

    const catRes = await AxiosHelper.post(AppsbdURL.route('categories/list'), {});
    if (catRes?.data?.rowdata) {
      categoryList.value = catRes.data.rowdata;
    }
  } catch (e) {
    console.error('Failed to load dependencies', e);
  }
}

function openCreateModal() {
  showModal.value = true;
}

async function saveTransaction(formData) {
  try {
    saving.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('transactions'), formData);
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Transaction recorded', 3000);
      showModal.value = false;
      await loadGridData();
    }
  } catch (e) {
    console.error(e);
  } finally {
    saving.value = false;
  }
}

async function deleteTransaction(id) {
  if (!confirm('Are you sure you want to delete this transaction?')) return;
  try {
    const res = await AxiosHelper.delete(AppsbdURL.route(`transactions/${id}`));
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Transaction deleted', 3000);
      await loadGridData();
    }
  } catch (e) {
    console.error(e);
  }
}

onMounted(async () => {
  await Promise.all([loadDependencies(), loadGridData()]);
});
</script>

<style scoped lang="scss">
.text-xxs {
  font-size: 0.72rem;
}
</style>
