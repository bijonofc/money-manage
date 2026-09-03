<template>
  <div class="debts-page pb-4">
    <!-- Top Filter & Action Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white">
      <div class="card-body p-3.5">
        <div class="row align-items-center g-3">
          <div class="col-12 col-lg-8">
            <apbd-filter-panel
              :filter-options="filterProps"
              @searchFilter="searchData"
              @reset="clearSearch"
            />
          </div>
          <div class="col-12 col-lg-4 d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
            <button
              class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 d-flex align-items-center gap-1.5 shadow-sm"
              @click="refreshGrid"
            >
              <RefreshCw :size="14" :class="{ 'spin-anim': isShowLoader }" />
              <span>Reload</span>
            </button>
            <button
              class="btn btn-danger btn-sm rounded-pill px-3.5 py-1.5 d-flex align-items-center gap-1.5 shadow-sm text-white fw-semibold"
              @click="openCreateModal"
            >
              <Plus :size="15" />
              <span>New Debt Record</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Elite Grid Data Table Container -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white p-3">
      <div class="elite-grid-container">
        <elite-grid
          :is-rounded="true"
          :is-group-separate-head="true"
          action-width="210px"
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
            <APBDGridLoader msg="Loading debt records..." />
          </template>

          <!-- No Record Slot -->
          <template #slot-no-record>
            <div class="p-5 text-center text-muted">
              <CreditCard :size="42" class="mx-auto mb-2.5 opacity-50" />
              <h6 class="fw-bold mb-1">No debt or loan records found</h6>
              <p class="text-xs mb-3 text-muted">Track money you owe to others or money others owe to you with real-time balance sync.</p>
              <button class="btn btn-danger text-white btn-sm rounded-pill px-3.5 py-1.5" @click="openCreateModal">
                + Add Debt Record
              </button>
            </div>
          </template>

          <!-- 1. Person / Creditor Slot -->
          <template #slotcreditor_name="{ rowitem }">
            <div>
              <span class="fw-bold text-dark d-block text-xs">{{ rowitem.creditor_name }}</span>
              <small class="text-muted text-xxs">{{ rowitem.creditor_contact || rowitem.description || 'No contact' }}</small>
            </div>
          </template>

          <!-- 2. Type Slot -->
          <template #slottype="{ rowitem }">
            <span
              class="badge text-capitalize text-xxs px-2 py-1 fw-semibold"
              :class="rowitem.type === 'owed_to' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle'"
            >
              {{ rowitem.type === 'owed_to' ? 'I Owe (Payable)' : 'Owed to Me (Receivable)' }}
            </span>
          </template>

          <!-- 3. Due Date Slot -->
          <template #slotdue_date="{ rowitem }">
            <span class="text-xs text-secondary">{{ formatDate(rowitem.due_date) }}</span>
          </template>

          <!-- 4. Principal Amount Slot -->
          <template #slotprincipal_amount="{ rowitem }">
            <span class="fw-semibold text-dark text-xs text-nowrap">৳ {{ formatNumber(rowitem.principal_amount) }}</span>
          </template>

          <!-- 5. Paid Amount Slot -->
          <template #slotpaid_amount="{ rowitem }">
            <span class="text-success fw-semibold text-xs text-nowrap">৳ {{ formatNumber(rowitem.paid_amount) }}</span>
          </template>

          <!-- 6. Remaining Amount Slot -->
          <template #slotremaining="{ rowitem }">
            <span
              class="fw-bold text-xs text-nowrap"
              :class="rowitem.status === 'paid' ? 'text-success' : 'text-danger'"
            >
              ৳ {{ formatNumber(Math.max(0, rowitem.principal_amount - rowitem.paid_amount)) }}
            </span>
          </template>

          <!-- 7. Status Slot -->
          <template #slotstatus="{ rowitem }">
            <span
              class="badge rounded-pill text-xxs px-2.5 py-1 fw-bold"
              :class="rowitem.status === 'paid' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'"
            >
              {{ rowitem.status === 'paid' ? 'Settled' : 'Active' }}
            </span>
          </template>

          <!-- 8. Actions Column Slot -->
          <template #actionProperty="{ rowitem }">
            <div class="d-flex align-items-center justify-content-end gap-1.5 flex-nowrap">
              <!-- Logs Button -->
              <button
                class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-0.5 text-xxs fw-semibold d-inline-flex align-items-center gap-1 shadow-sm"
                @click="openLogsModal(rowitem)"
                title="View Payment Logs"
              >
                <History :size="12" />
                <span>Logs</span>
                <span v-if="rowitem.payments && rowitem.payments.length" class="badge bg-primary text-white rounded-pill px-1.5 py-0.5 text-xxs">
                  {{ rowitem.payments.length }}
                </span>
              </button>

              <!-- Pay / Collect Button -->
              <button
                v-if="rowitem.status !== 'paid'"
                class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-0.5 text-xxs fw-semibold shadow-sm"
                @click="openPayModal(rowitem)"
              >
                {{ rowitem.type === 'owed_to' ? '+ Pay' : '+ Collect' }}
              </button>

              <!-- Delete Button -->
              <button
                class="btn btn-icon btn-light btn-sm rounded-circle text-danger p-1"
                @click="deleteDebt(rowitem.id)"
                title="Delete Record"
              >
                <Trash2 :size="13" />
              </button>
            </div>
          </template>
        </elite-grid>
      </div>
    </div>

    <!-- Create Modal -->
    <div v-if="showModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
      <div class="modal-card bg-white rounded-4 shadow-lg p-4" style="max-width: 520px; width: 100%;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <CreditCard :size="18" class="text-danger" />
            Record Debt / Loan
          </h5>
          <button type="button" class="btn-close" @click="showModal = false"></button>
        </div>

        <form @submit.prevent="saveDebt">
          <!-- Debt Type -->
          <div class="mb-3">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Type *</label>
            <div class="btn-group w-100" role="group">
              <input
                type="radio"
                class="btn-check"
                name="debt_type"
                id="type_owed_to"
                value="owed_to"
                v-model="form.type"
              />
              <label class="btn btn-outline-danger text-xs fw-semibold py-2" for="type_owed_to">
                I Borrowed Money (Payable)
              </label>

              <input
                type="radio"
                class="btn-check"
                name="debt_type"
                id="type_owed_from"
                value="owed_from"
                v-model="form.type"
              />
              <label class="btn btn-outline-success text-xs fw-semibold py-2" for="type_owed_from">
                I Lent Money (Receivable)
              </label>
            </div>
          </div>

          <!-- Creditor Name & Contact -->
          <div class="row g-2 mb-3">
            <div class="col-7">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">
                {{ form.type === 'owed_to' ? 'Lender / Creditor Name *' : 'Borrower / Debtor Name *' }}
              </label>
              <input v-model="form.creditor_name" type="text" class="form-control text-xs" placeholder="e.g. John Doe / Bank" required />
            </div>
            <div class="col-5">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Phone / Contact</label>
              <input v-model="form.creditor_contact" type="text" class="form-control text-xs" placeholder="+8801..." />
            </div>
          </div>

          <!-- Principal Amount & Due Date -->
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Principal Amount (৳) *</label>
              <input v-model.number="form.principal_amount" type="number" step="0.01" min="0.01" class="form-control text-xs fw-bold" placeholder="0.00" required />
            </div>
            <div class="col-6">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Due Date (Optional)</label>
              <input v-model="form.due_date" type="date" class="form-control text-xs" />
            </div>
          </div>

          <!-- Auto-Account Inflow/Outflow Checkbox & Account Selector -->
          <div class="card bg-light border-0 rounded-3 p-3 mb-3">
            <div class="form-check form-switch mb-2">
              <input
                id="syncAccountCheckbox"
                v-model="form.sync_account"
                class="form-check-input"
                type="checkbox"
              />
              <label class="form-check-label text-xs fw-semibold text-dark" for="syncAccountCheckbox">
                {{ form.type === 'owed_to' ? 'Deposit borrowed funds into an account' : 'Disburse funds from an account' }}
              </label>
            </div>

            <div v-if="form.sync_account" class="mt-2 animate-fade-in">
              <label class="form-label text-xxs text-muted fw-bold text-uppercase mb-1">
                {{ form.type === 'owed_to' ? 'Deposit Into Account *' : 'Withdraw From Account *' }}
              </label>
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted">
                  <Wallet :size="14" />
                </span>
                <select v-model="form.account_id" class="form-select border-start-0 text-xs" :required="form.sync_account">
                  <option value="" disabled>Select account...</option>
                  <option v-for="acc in accountList" :key="acc.id" :value="acc.id">
                    {{ acc.name }} (৳{{ formatNumber(acc.balance) }})
                  </option>
                </select>
              </div>
              <small class="text-xxs text-muted mt-1 d-block">
                {{ form.type === 'owed_to' ? '✨ Will automatically record an inflow and increase your account balance.' : '✨ Will automatically record an outflow expense and decrease your account balance.' }}
              </small>
            </div>
          </div>

          <!-- Notes / Purpose -->
          <div class="mb-3">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Notes / Purpose (Optional)</label>
            <input v-model="form.description" type="text" class="form-control text-xs" placeholder="e.g. Emergency medical expenses" />
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4 text-xs fw-semibold" @click="showModal = false">Cancel</button>
            <button type="submit" class="btn btn-danger text-white rounded-pill px-4 text-xs fw-semibold" :disabled="saving">
              {{ saving ? 'Saving...' : 'Save Debt Record' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Pay / Repay Modal -->
    <div v-if="showPayModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
      <div class="modal-card bg-white rounded-4 shadow-lg p-4" style="max-width: 440px; width: 100%;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <CreditCard :size="18" :class="selectedDebt?.type === 'owed_to' ? 'text-danger' : 'text-success'" />
            {{ selectedDebt?.type === 'owed_to' ? 'Record Repayment' : 'Collect Loan Payment' }}
          </h5>
          <button type="button" class="btn-close" @click="showPayModal = false"></button>
        </div>

        <div v-if="selectedDebt" class="alert alert-light border p-2.5 rounded-3 mb-3 text-xs">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Person:</span>
            <span class="fw-bold text-dark">{{ selectedDebt.creditor_name }}</span>
          </div>
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Remaining Balance:</span>
            <span class="fw-bold text-danger">৳{{ formatNumber(Math.max(0, selectedDebt.principal_amount - selectedDebt.paid_amount)) }}</span>
          </div>
        </div>

        <form @submit.prevent="savePayment">
          <!-- Payment Amount -->
          <div class="mb-3">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Payment Amount (৳) *</label>
            <input
              v-model.number="paymentAmount"
              type="number"
              step="0.01"
              min="0.01"
              class="form-control text-xs fw-bold"
              placeholder="0.00"
              required
            />
          </div>

          <!-- Account to Pay From / Deposit Into -->
          <div class="mb-3">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">
              {{ selectedDebt?.type === 'owed_to' ? 'Pay From Account (Deduct Balance) *' : 'Deposit Into Account *' }}
            </label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted">
                <Wallet :size="14" />
              </span>
              <select v-model="paymentAccountId" class="form-select border-start-0 text-xs" required>
                <option value="" disabled>Select account...</option>
                <option v-for="acc in accountList" :key="acc.id" :value="acc.id">
                  {{ acc.name }} (৳{{ formatNumber(acc.balance) }})
                </option>
              </select>
            </div>
            <small class="text-xxs text-muted mt-1 d-block">
              {{ selectedDebt?.type === 'owed_to' ? '✨ Will record an expense transaction and subtract from this account.' : '✨ Will record an income transaction and add to this account.' }}
            </small>
          </div>

          <!-- Date & Time -->
          <div class="row g-2 mb-3">
            <div class="col-7">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Date *</label>
              <input v-model="paymentDate" type="date" class="form-control text-xs" required />
            </div>
            <div class="col-5">
              <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">Time</label>
              <input v-model="paymentTime" type="time" class="form-control text-xs" />
            </div>
          </div>

          <!-- Note / Details (MANDATORY) -->
          <div class="mb-3">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1">
              Note / Details <span class="text-danger">*</span>
            </label>
            <input
              v-model="paymentNote"
              type="text"
              class="form-control text-xs"
              placeholder="e.g. Paid via bKash / Monthly installment"
              required
            />
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4 text-xs fw-semibold" @click="showPayModal = false">Cancel</button>
            <button
              type="submit"
              class="btn text-white rounded-pill px-4 text-xs fw-semibold"
              :class="selectedDebt?.type === 'owed_to' ? 'btn-danger' : 'btn-success'"
              :disabled="saving"
            >
              {{ saving ? 'Saving...' : (selectedDebt?.type === 'owed_to' ? 'Record Repayment' : 'Record Collection') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Payment Logs / History Modal -->
    <div v-if="showLogsModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
      <div class="modal-card bg-white rounded-4 shadow-lg p-4" style="max-width: 660px; width: 100%;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
              <History :size="20" class="text-primary" />
              Payment History Logs
            </h5>
            <small class="text-muted text-xxs">
              {{ selectedDebtLogs?.creditor_name }} ·
              <span :class="selectedDebtLogs?.type === 'owed_to' ? 'text-danger' : 'text-success'">
                {{ selectedDebtLogs?.type === 'owed_to' ? 'Money Owed (Payable)' : 'Money Lent (Receivable)' }}
              </span>
            </small>
          </div>
          <button type="button" class="btn-close" @click="showLogsModal = false"></button>
        </div>

        <div v-if="selectedDebtLogs" class="logs-summary-box mb-4">
          <!-- KPI Stats Card -->
          <div class="row g-2 mb-3">
            <div class="col-4">
              <div class="p-2.5 rounded-3 bg-light text-center">
                <span class="text-xxs text-muted fw-bold text-uppercase d-block mb-1">Principal</span>
                <span class="fw-bold text-dark text-xs">৳{{ formatNumber(selectedDebtLogs.principal_amount) }}</span>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2.5 rounded-3 bg-success-subtle text-center">
                <span class="text-xxs text-success fw-bold text-uppercase d-block mb-1">Total Paid</span>
                <span class="fw-bold text-success text-xs">৳{{ formatNumber(selectedDebtLogs.paid_amount) }}</span>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2.5 rounded-3 bg-danger-subtle text-center">
                <span class="text-xxs text-danger fw-bold text-uppercase d-block mb-1">Remaining</span>
                <span class="fw-bold text-danger text-xs">
                  ৳{{ formatNumber(Math.max(0, selectedDebtLogs.principal_amount - selectedDebtLogs.paid_amount)) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Progress Bar -->
          <div class="progress rounded-pill mb-1" style="height: 6px;">
            <div
              class="progress-bar bg-success rounded-pill"
              role="progressbar"
              :style="{ width: getPaymentProgress(selectedDebtLogs) + '%' }"
            ></div>
          </div>
          <div class="d-flex justify-content-between text-xxs text-muted">
            <span>{{ getPaymentProgress(selectedDebtLogs) }}% Settled</span>
            <span>Status: <strong>{{ selectedDebtLogs.status === 'paid' ? 'Completed' : 'Active' }}</strong></span>
          </div>
        </div>

        <!-- Logs Content Table / List -->
        <div class="payment-logs-container">
          <h6 class="text-xs fw-bold text-uppercase tracking-wider text-muted mb-2.5 d-flex align-items-center justify-content-between">
            <span>Logged Repayments ({{ selectedDebtLogs?.payments?.length || 0 }})</span>
            <button
              v-if="selectedDebtLogs && selectedDebtLogs.status !== 'paid'"
              type="button"
              class="btn btn-outline-success btn-sm rounded-pill px-2.5 py-0.5 text-xxs fw-semibold"
              @click="switchFromLogsToPay(selectedDebtLogs)"
            >
              + Record Payment
            </button>
          </h6>

          <!-- Empty State -->
          <div v-if="!selectedDebtLogs?.payments || selectedDebtLogs.payments.length === 0" class="text-center py-4 text-muted bg-light rounded-3">
            <Receipt :size="28" class="opacity-50 mb-1.5" />
            <p class="text-xs mb-0">No payment logs recorded yet for this debt.</p>
          </div>

          <!-- Payment Entries List -->
          <div v-else class="table-responsive border rounded-3 overflow-hidden">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light text-xxs text-uppercase tracking-wider text-muted">
                <tr>
                  <th class="ps-3 border-0">Note / Details</th>
                  <th class="border-0 text-nowrap" style="width: 140px;">Account</th>
                  <th class="border-0 text-nowrap" style="width: 130px;">Date & Time</th>
                  <th class="border-0 text-end pe-3 text-nowrap" style="width: 130px;">Amount</th>
                </tr>
              </thead>
              <tbody class="text-xs">
                <tr v-for="(p, index) in selectedDebtLogs.payments" :key="p.id">
                  <!-- 1. Note / Details (FIRST COLUMN) -->
                  <td class="ps-3">
                    <div class="d-flex align-items-center gap-2">
                      <span class="badge bg-light text-muted border text-xxs px-1.5 py-0.5">#{{ index + 1 }}</span>
                      <span class="fw-semibold text-dark">{{ p.note || p.transaction?.description || 'Repayment entry' }}</span>
                    </div>
                  </td>

                  <!-- 2. Account -->
                  <td class="text-nowrap">
                    <span v-if="p.transaction?.account" class="badge bg-light text-dark border text-xxs px-2 py-1">
                      <Wallet :size="12" class="me-1 text-primary" />
                      {{ p.transaction.account.name }}
                    </span>
                    <span v-else class="text-muted text-xxs">—</span>
                  </td>

                  <!-- 3. Date & Time -->
                  <td class="text-nowrap">
                    <span class="fw-medium text-dark d-block">{{ formatDate(p.payment_date) }}</span>
                    <small v-if="p.transaction?.time" class="text-muted text-xxs">{{ p.transaction.time }}</small>
                    <small v-else class="text-muted text-xxs">Recorded</small>
                  </td>

                  <!-- 4. Amount (NO WRAP) -->
                  <td class="text-end pe-3 text-nowrap" style="white-space: nowrap;">
                    <span class="fw-bold text-success text-nowrap" style="white-space: nowrap; display: inline-block;">
                      +৳{{ formatNumber(p.amount) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
          <button type="button" class="btn btn-secondary rounded-pill px-4 text-xs fw-semibold" @click="showLogsModal = false">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js';
import AppsbdURL from '@/libs/AppsbdURL.js';
import AppsbdUtls from '@/libs/AppsbdUtls.js';
import APBDRequestParam from '@/libs/APBDRequestParam';
import APBDGridLoader from '@/components/APBDGridLoader.vue';
import EliteGrid from '@appsbd/vue3-elite-grid';
import { EliteColumnModel } from '@appsbd/vue3-elite-grid';
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs';

import {
  CreditCard,
  Plus,
  Trash2,
  RefreshCw,
  Wallet,
  History,
  Receipt,
} from '@lucide/vue';

const isShowLoader = ref(false);
const saving = ref(false);
const showModal = ref(false);
const showPayModal = ref(false);
const showLogsModal = ref(false);

const accountList = ref([]);
const selectedDebt = ref(null);
const selectedDebtLogs = ref(null);

const paymentAmount = ref('');
const paymentAccountId = ref('');
const paymentDate = ref(new Date().toISOString().split('T')[0]);
const paymentTime = ref(new Date().toTimeString().slice(0, 5));
const paymentNote = ref('');

const currencySymbol = computed(() => window.app_settings?.currencySymbol || '৳');

const gridData = reactive({
  page: 1,
  total: 1,
  records: 0,
  limit: 20,
  rowdata: [],
});

const searchProps = ref([]);
const sortProps = ref(null);

const data_column = [
  EliteColumnModel.getColumn({ name: 'creditor_name', title: 'Person / Creditor', width: '180px', is_sortable: true }),
  EliteColumnModel.getColumn({ name: 'type', title: 'Type', width: '150px', is_sortable: true }),
  EliteColumnModel.getColumn({ name: 'due_date', title: 'Due Date', width: '130px', is_sortable: true }),
  EliteColumnModel.getColumn({ name: 'principal_amount', title: 'Principal', width: '120px', is_sortable: true }),
  EliteColumnModel.getColumn({ name: 'paid_amount', title: 'Paid', width: '110px', is_sortable: true }),
  EliteColumnModel.getColumn({ name: 'remaining', title: 'Remaining', width: '120px', is_sortable: false }),
  EliteColumnModel.getColumn({ name: 'status', title: 'Status', width: '100px', is_sortable: true }),
];

const filterProps = [
  {
    id: 1,
    name: 'Person / Creditor',
    propName: 'creditor_name',
    type: 't',
    options: [],
    operators: 'like',
    value: '',
  },
  {
    id: 2,
    name: 'Type',
    propName: 'type',
    type: 'dd',
    optionLabel: 'name',
    optionValueProp: 'val',
    options: [
      { name: 'I Owe (Payable)', val: 'owed_to' },
      { name: 'Owed to Me (Receivable)', val: 'owed_from' },
    ],
    operators: 'eq',
    value: '',
  },
  {
    id: 3,
    name: 'Status',
    propName: 'status',
    type: 'dd',
    optionLabel: 'name',
    optionValueProp: 'val',
    options: [
      { name: 'Active', val: 'active' },
      { name: 'Settled', val: 'paid' },
    ],
    operators: 'eq',
    value: '',
  },
];

const form = ref({
  type: 'owed_to',
  creditor_name: '',
  creditor_contact: '',
  principal_amount: '',
  due_date: '',
  description: '',
  sync_account: true,
  account_id: '',
});

function formatNumber(val) {
  const n = parseFloat(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(dateStr) {
  if (!dateStr) return '—';
  const clean = dateStr.includes('T') ? dateStr.split('T')[0] : dateStr;
  const parts = clean.split('-');
  if (parts.length === 3) {
    const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
    return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
  }
  return clean;
}

function getPaymentProgress(debt) {
  if (!debt || !debt.principal_amount || debt.principal_amount <= 0) return 0;
  const pct = (parseFloat(debt.paid_amount || 0) / parseFloat(debt.principal_amount)) * 100;
  return Math.min(Math.round(pct), 100);
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

async function loadGridData() {
  isShowLoader.value = true;
  const param = new APBDRequestParam();
  param.limit = gridData.limit;
  param.page = gridData.page;

  for (const item of searchProps.value) {
    if (item.value !== '' && item.value !== null && item.value !== undefined) {
      param.AddSrcItem(item.propName, item.value, item.operators);
    }
  }

  if (sortProps.value) {
    param.AddSortItem(sortProps.value.prop, sortProps.value.ord);
  }

  try {
    const res = await AxiosHelper.post(AppsbdURL.route('debts/list'), param);
    if (res?.data) {
      gridData.page = res.data.page || 1;
      gridData.limit = res.data.limit || 20;
      gridData.records = res.data.records || 0;
      gridData.total = res.data.total || 1;
      gridData.rowdata = res.data.rowdata || [];

      // If active logs modal is open, refresh its data
      if (selectedDebtLogs.value) {
        const updated = gridData.rowdata.find(d => d.id === selectedDebtLogs.value.id);
        if (updated) selectedDebtLogs.value = updated;
      }
    }
  } catch (e) {
    console.error('Failed to load debt grid data', e);
  } finally {
    isShowLoader.value = false;
  }
}

function searchData(data) {
  searchProps.value = data;
  gridData.page = 1;
  loadGridData();
}

function clearSearch() {
  searchProps.value = [];
  loadGridData();
}

function refreshGrid() {
  loadGridData();
}

function eliteGridLoadData(gparam) {
  gridData.limit = gparam.limit;
  gridData.page = gparam.page;
  sortProps.value = gparam.sort_prop ? { prop: gparam.sort_prop, ord: gparam.sort_ord } : null;
  loadGridData();
}

function openCreateModal() {
  form.value = {
    type: 'owed_to',
    creditor_name: '',
    creditor_contact: '',
    principal_amount: '',
    due_date: '',
    description: '',
    sync_account: true,
    account_id: accountList.value[0]?.id || '',
  };
  showModal.value = true;
}

function openPayModal(debt) {
  selectedDebt.value = debt;
  paymentAmount.value = Math.max(0, debt.principal_amount - debt.paid_amount);
  paymentAccountId.value = accountList.value[0]?.id || '';
  paymentDate.value = new Date().toISOString().split('T')[0];
  paymentTime.value = new Date().toTimeString().slice(0, 5);
  paymentNote.value = '';
  showPayModal.value = true;
}

function openLogsModal(debt) {
  selectedDebtLogs.value = debt;
  showLogsModal.value = true;
}

function switchFromLogsToPay(debt) {
  showLogsModal.value = false;
  openPayModal(debt);
}

async function saveDebt() {
  try {
    saving.value = true;
    const payload = {
      type: form.value.type,
      creditor_name: form.value.creditor_name,
      creditor_contact: form.value.creditor_contact,
      principal_amount: form.value.principal_amount,
      due_date: form.value.due_date || null,
      description: form.value.description,
      account_id: form.value.sync_account ? form.value.account_id : null,
    };

    const res = await AxiosHelper.post(AppsbdURL.route('debts'), payload);
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Debt record created successfully', 3000);
      showModal.value = false;
      await Promise.all([loadGridData(), loadAccounts()]);
    }
  } catch (e) {
    console.error(e);
  } finally {
    saving.value = false;
  }
}

async function savePayment() {
  if (!selectedDebt.value) return;
  try {
    saving.value = true;
    const payload = {
      amount: paymentAmount.value,
      account_id: paymentAccountId.value,
      payment_date: paymentDate.value,
      time: paymentTime.value,
      note: paymentNote.value,
    };

    const res = await AxiosHelper.post(AppsbdURL.route(`debts/${selectedDebt.value.id}/pay`), payload);
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Payment recorded successfully', 3000);
      showPayModal.value = false;
      await Promise.all([loadGridData(), loadAccounts()]);
    }
  } catch (e) {
    console.error(e);
  } finally {
    saving.value = false;
  }
}

async function deleteDebt(id) {
  if (!confirm('Are you sure you want to delete this debt record?')) return;
  try {
    const res = await AxiosHelper.delete(AppsbdURL.route(`debts/${id}`));
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Record deleted', 3000);
      await loadGridData();
    }
  } catch (e) {
    console.error(e);
  }
}

onMounted(async () => {
  await Promise.all([loadAccounts(), loadGridData()]);
});
</script>

<style scoped lang="scss">
.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.text-xxs {
  font-size: 0.6875rem;
}

.tracking-wider {
  letter-spacing: 0.05em;
}

.animate-fade-in {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
