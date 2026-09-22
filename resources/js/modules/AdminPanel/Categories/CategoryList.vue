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
          <p class="text-muted small mb-0">Organize your income and expenses into meaningful categories with custom icons and colors</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <ab-button color="secondary" is-outline :is-animated="loading" :disabled="loading" @click="loadCategories">
            Refresh
          </ab-button>
          <ab-button color="primary" @click="openCreateModal">
            <Plus :size="16" class="me-1" />
            New Category
          </ab-button>
        </div>
      </div>
    </div>

    <!-- Category Tabs, Filter & Search -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <!-- Type Filter Buttons -->
        <div class="d-flex align-items-center gap-2">
          <button
            type="button"
            class="filter-pill-btn btn btn-sm rounded-pill px-3"
            :class="activeType === 'all' ? 'btn-primary' : 'btn-light text-muted'"
            @click="activeType = 'all'"
          >
            All <span class="badge rounded-pill ms-1" :class="activeType === 'all' ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary'">{{ categories.length }}</span>
          </button>
          <button
            type="button"
            class="filter-pill-btn btn btn-sm rounded-pill px-3"
            :class="activeType === 'expense' ? 'btn-danger' : 'btn-light text-muted'"
            @click="activeType = 'expense'"
          >
            Expenses <span class="badge rounded-pill ms-1" :class="activeType === 'expense' ? 'bg-white text-danger' : 'bg-secondary-subtle text-secondary'">{{ expenseCount }}</span>
          </button>
          <button
            type="button"
            class="filter-pill-btn btn btn-sm rounded-pill px-3"
            :class="activeType === 'income' ? 'btn-success' : 'btn-light text-muted'"
            @click="activeType = 'income'"
          >
            Income <span class="badge rounded-pill ms-1" :class="activeType === 'income' ? 'bg-white text-success' : 'bg-secondary-subtle text-secondary'">{{ incomeCount }}</span>
          </button>
        </div>

        <!-- Search Bar -->
        <div class="search-box-wrap" style="max-width: 280px; width: 100%;">
          <div class="input-group input-group-sm rounded-pill overflow-hidden border bg-light">
            <span class="input-group-text bg-transparent border-0 text-muted ps-3">
              <Search :size="14" />
            </span>
            <input
              v-model="searchQuery"
              type="text"
              class="form-control bg-transparent border-0 ps-1 text-xs"
              placeholder="Search category..."
            />
            <button
              v-if="searchQuery"
              type="button"
              class="btn btn-link btn-sm text-muted pe-3 text-decoration-none"
              @click="searchQuery = ''"
            >
              <X :size="13" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Category Grid -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else-if="filteredCategories.length === 0" class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
      <Tag :size="48" class="mx-auto mb-3 opacity-50 text-secondary" />
      <h5>No categories found</h5>
      <p class="small mb-4">{{ searchQuery ? `No categories matching "${searchQuery}"` : 'Create your first category to organize transactions.' }}</p>
      <div>
        <ab-button color="primary" @click="openCreateModal">
          <Plus :size="16" class="me-1" />
          Create Category
        </ab-button>
      </div>
    </div>

    <div v-else class="row g-3">
      <div v-for="cat in filteredCategories" :key="cat.id" class="col-12 col-sm-6 col-lg-4 col-xl-3">
        <div
          class="card rounded-4 category-card transition-all d-flex flex-row align-items-center gap-3 p-3 h-100 shadow-xs"
          :class="cat.type === 'income' ? 'card-income' : 'card-expense'"
        >
          <!-- Icon Badge with Category Color -->
          <div
            class="cat-color-badge rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0 shadow-xs"
            :style="{ backgroundColor: cat.color || '#6366f1' }"
          >
            <component :is="resolveCategoryIcon(cat.icon || cat.name)" :size="18" />
          </div>

          <!-- Name on top, compact buttons below -->
          <div class="min-w-0 flex-grow-1">
            <h6 class="fw-bold mb-1 text-dark text-truncate" :title="cat.name">
              {{ cat.name }}
            </h6>
            <div class="d-flex align-items-center gap-1.5">
              <button
                type="button"
                class="btn btn-action-compact rounded-circle p-0 d-inline-flex align-items-center justify-content-center"
                title="Edit category"
                @click="openEditModal(cat)"
              >
                <Pencil :size="11" class="text-secondary" />
              </button>
              <button
                type="button"
                class="btn btn-action-compact rounded-circle p-0 d-inline-flex align-items-center justify-content-center text-danger"
                title="Delete category"
                @click="deleteCategory(cat.id)"
              >
                <Trash2 :size="11" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Category Create & Edit Form Modal -->
    <CategoryFormModal
      v-model="showModal"
      :edit-data="selectedCategory"
      :saving="saving"
      @save="saveCategory"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js';
import AppsbdURL from '@/libs/AppsbdURL.js';
import AppsbdUtls from '@/libs/AppsbdUtls.js';
import CategoryFormModal from './CategoryFormModal.vue';
import { resolveCategoryIcon } from '@/libs/CategoryIcons.js';

import {
  Tag,
  Trash2,
  Pencil,
  Plus,
  Search,
  X,
} from '@lucide/vue';

const categories = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const selectedCategory = ref(null);
const activeType = ref('all');
const searchQuery = ref('');

const expenseCount = computed(() => {
  return categories.value.filter((c) => c.type === 'expense').length;
});

const incomeCount = computed(() => {
  return categories.value.filter((c) => c.type === 'income').length;
});

const filteredCategories = computed(() => {
  let list = categories.value;
  if (activeType.value !== 'all') {
    list = list.filter((c) => c.type === activeType.value);
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim();
    list = list.filter((c) => (c.name || '').toLowerCase().includes(q));
  }
  return list;
});

async function loadCategories() {
  try {
    loading.value = true;
    const res = await AxiosHelper.post(AppsbdURL.route('categories/list'), {});
    if (res?.data?.rowdata) {
      categories.value = res.data.rowdata;
    }
  } catch (e) {
    console.error('Failed to load categories', e);
  } finally {
    loading.value = false;
  }
}

function openCreateModal() {
  selectedCategory.value = null;
  showModal.value = true;
}

function openEditModal(cat) {
  selectedCategory.value = { ...cat };
  showModal.value = true;
}

async function saveCategory(formData) {
  try {
    saving.value = true;
    let res;
    if (formData.id) {
      res = await AxiosHelper.put(AppsbdURL.route(`categories/${formData.id}`), formData);
    } else {
      res = await AxiosHelper.post(AppsbdURL.route('categories'), formData);
    }

    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(
        res.msg || (formData.id ? 'Category updated successfully' : 'Category created successfully'),
        3000
      );
      showModal.value = false;
      await loadCategories();
    }
  } catch (e) {
    console.error('Failed to save category', e);
  } finally {
    saving.value = false;
  }
}

async function deleteCategory(id) {
  if (!confirm('Are you sure you want to delete this category?')) return;
  try {
    const res = await AxiosHelper.delete(AppsbdURL.route(`categories/${id}`));
    if (res?.status) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Category deleted successfully', 3000);
      await loadCategories();
    }
  } catch (e) {
    console.error('Failed to delete category', e);
  }
}

onMounted(loadCategories);
</script>

<style scoped lang="scss">
.cat-color-badge {
  width: 38px;
  height: 38px;
  min-width: 38px;
}

.category-card {
  border-width: 1px;
  border-style: solid;
  transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
  overflow: hidden;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.06) !important;
  }

  // Reddish-white for expense categories
  &.card-expense {
    background: linear-gradient(135deg, #fff7f7 0%, #ffffff 100%);
    border-color: #fee2e2 !important;

    &:hover {
      border-color: #fca5a5 !important;
    }
  }

  // Theme-white / greenish-white for income categories
  &.card-income {
    background: linear-gradient(135deg, #f4fbf7 0%, #ffffff 100%);
    border-color: #d1fae5 !important;

    &:hover {
      border-color: #86efac !important;
    }
  }
}

.btn-action-compact {
  width: 24px;
  height: 24px;
  background-color: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
  transition: transform 0.15s ease, background-color 0.15s ease, border-color 0.15s ease;

  &:hover {
    background-color: #ffffff;
    border-color: rgba(0, 0, 0, 0.18);
    transform: scale(1.12);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
  }
}

.filter-pill-btn {
  font-weight: 500;
  transition: all 0.15s ease;
}

.text-xs {
  font-size: 0.8125rem;
}

[data-bs-theme="dark"] {
  .category-card {
    &.card-expense {
      background: rgba(239, 68, 68, 0.08) !important;
      border-color: rgba(239, 68, 68, 0.22) !important;
      &:hover {
        border-color: rgba(239, 68, 68, 0.4) !important;
      }
    }
    &.card-income {
      background: rgba(16, 185, 129, 0.08) !important;
      border-color: rgba(16, 185, 129, 0.22) !important;
      &:hover {
        border-color: rgba(16, 185, 129, 0.4) !important;
      }
    }
  }
  .btn-action-compact {
    background-color: #1e293b !important;
    border-color: #334155 !important;
    &:hover {
      background-color: #334155 !important;
    }
  }
}
</style>
