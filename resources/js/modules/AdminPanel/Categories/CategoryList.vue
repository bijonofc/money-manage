<template>
  <div class="categories-page pb-4">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
          <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
            <Tag :size="24" class="text-primary" />
            Categories
          </h4>
          <p class="text-muted small mb-0">Organize your income and expenses into meaningful categories</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2" @click="loadCategories">
            <RefreshCw :size="15" :class="{ 'spin-anim': loading }" />
            <span>Refresh</span>
          </button>
          <button class="btn btn-primary btn-sm rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-sm" @click="openCreateModal">
            <Plus :size="16" />
            <span>New Category</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Category Tabs / Filter -->
    <div class="card border-0 shadow-sm rounded-4 mb-3 p-2">
      <div class="d-flex gap-2">
        <button
          class="btn btn-sm rounded-pill px-4"
          :class="activeType === 'all' ? 'btn-primary' : 'btn-light'"
          @click="activeType = 'all'"
        >
          All
        </button>
        <button
          class="btn btn-sm rounded-pill px-4"
          :class="activeType === 'expense' ? 'btn-danger text-white' : 'btn-light'"
          @click="activeType = 'expense'"
        >
          Expenses
        </button>
        <button
          class="btn btn-sm rounded-pill px-4"
          :class="activeType === 'income' ? 'btn-success text-white' : 'btn-light'"
          @click="activeType = 'income'"
        >
          Income
        </button>
      </div>
    </div>

    <!-- Category Grid -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else-if="filteredCategories.length === 0" class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
      <Tag :size="48" class="mx-auto mb-3 opacity-50" />
      <h5>No categories found</h5>
      <p class="small mb-4">Create your first category to organize transactions.</p>
      <div>
        <button class="btn btn-primary rounded-pill px-4 py-2" @click="openCreateModal">
          Create Category
        </button>
      </div>
    </div>

    <div v-else class="row g-3">
      <div v-for="cat in filteredCategories" :key="cat.id" class="col-12 col-sm-6 col-md-4 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 d-flex flex-row align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div
              class="cat-color-badge rounded-circle p-2 d-flex align-items-center justify-content-center text-white"
              :style="{ backgroundColor: cat.color || '#6366f1' }"
            >
              <Tag :size="16" />
            </div>
            <div>
              <h6 class="fw-bold mb-0 text-dark">{{ cat.name }}</h6>
              <span
                class="badge small text-capitalize"
                :class="cat.type === 'income' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'"
              >
                {{ cat.type }}
              </span>
            </div>
          </div>
          <button class="btn btn-icon btn-light btn-sm rounded-circle text-danger" @click="deleteCategory(cat.id)">
            <Trash2 :size="14" />
          </button>
        </div>
      </div>
    </div>

    <!-- Create Modal -->
    <div v-if="showModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
      <div class="modal-card bg-white rounded-4 shadow-lg p-4" style="max-width: 440px; width: 100%;">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h5 class="fw-bold mb-0">New Category</h5>
          <button type="button" class="btn-close" @click="showModal = false"></button>
        </div>

        <form @submit.prevent="saveCategory">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Category Name *</label>
            <input v-model="form.name" type="text" class="form-control" placeholder="e.g. Housing / Rent" required />
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Type *</label>
            <select v-model="form.type" class="form-select" required>
              <option value="expense">Expense</option>
              <option value="income">Income</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Color</label>
            <input v-model="form.color" type="color" class="form-control form-control-color w-100" />
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4" @click="showModal = false">Cancel</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4" :disabled="saving">
              {{ saving ? 'Saving...' : 'Save Category' }}
            </button>
          </div>
        </form>
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
  Tag,
  Plus,
  Trash2,
  RefreshCw,
} from '@lucide/vue';

const categories = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const activeType = ref('all');

const form = ref({
  name: '',
  type: 'expense',
  color: '#6366f1',
});

const filteredCategories = computed(() => {
  if (activeType.value === 'all') return categories.value;
  return categories.value.filter(c => c.type === activeType.value);
});

async function loadCategories() {
  try {
    loading.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('categories/list'), {});
    if (res?.data?.rowdata) {
      categories.value = res.data.rowdata;
    }
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

function openCreateModal() {
  form.value = {
    name: '',
    type: 'expense',
    color: '#6366f1',
  };
  showModal.value = true;
}

async function saveCategory() {
  try {
    saving.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('categories'), form.value);
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Category created', 3000);
      showModal.value = false;
      await loadCategories();
    }
  } catch (e) {
    console.error(e);
  } finally {
    saving.value = false;
  }
}

async function deleteCategory(id) {
  if (!confirm('Are you sure you want to delete this category?')) return;
  try {
    const res = await AxiosHelper.delete(AppsbdURL.route(`categories/${id}`));
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Category deleted', 3000);
      await loadCategories();
    }
  } catch (e) {
    console.error(e);
  }
}

onMounted(loadCategories);
</script>

<style scoped lang="scss">
.cat-color-badge {
  width: 34px;
  height: 34px;
}

.modal-backdrop-custom {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  z-index: 1050;
  backdrop-filter: blur(2px);
}

.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
