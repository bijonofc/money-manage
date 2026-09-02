<template>
  <div class="category-picker-wrapper">
    <!-- Header with label, count, and search toggle / add button -->
    <div class="d-flex align-items-center justify-content-between mb-2">
      <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-0 d-flex align-items-center gap-1.5">
        <Tag :size="14" class="text-primary" />
        <span>Category <span class="text-danger">*</span></span>
      </label>

      <div class="d-flex align-items-center gap-1.5">
        <!-- Search Toggle Button (if more than 6 categories) -->
        <button
          v-if="filteredCategories.length > 6 || searchQuery"
          type="button"
          class="btn btn-xs btn-light rounded-pill px-2 py-0.5 text-xxs d-flex align-items-center gap-1 text-muted"
          @click="toggleSearch"
        >
          <Search :size="11" />
          <span>{{ showSearch ? 'Hide Search' : 'Search' }}</span>
        </button>

        <!-- Inline Add Category Button -->
        <button
          type="button"
          class="btn btn-xs btn-light-primary rounded-pill px-2.5 py-0.5 text-xxs fw-semibold d-flex align-items-center gap-1"
          @click="showInlineCreate = !showInlineCreate"
        >
          <Plus :size="11" />
          <span>{{ showInlineCreate ? 'Cancel' : 'New Category' }}</span>
        </button>
      </div>
    </div>

    <!-- Quick Search Input Bar -->
    <div v-if="showSearch" class="mb-2.5 animate-fade-in">
      <div class="input-group input-group-sm rounded-pill overflow-hidden border bg-light">
        <span class="input-group-text bg-transparent border-0 text-muted ps-2.5">
          <Search :size="13" />
        </span>
        <input
          ref="searchInputRef"
          v-model="searchQuery"
          type="text"
          class="form-control form-control-sm bg-transparent border-0 ps-1 text-xs"
          placeholder="Type to filter categories..."
        />
        <button
          v-if="searchQuery"
          type="button"
          class="btn btn-sm btn-link text-muted p-1 pe-2"
          @click="searchQuery = ''"
        >
          <X :size="12" />
        </button>
      </div>
    </div>

    <!-- INLINE NEW CATEGORY CREATOR (Expandable) -->
    <div v-if="showInlineCreate" class="inline-creator-card p-3 rounded-3 bg-light border border-dashed mb-3 animate-fade-in">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-xs fw-bold text-dark d-flex align-items-center gap-1">
          <Sparkles :size="13" class="text-warning" />
          <span>Create New {{ type === 'income' ? 'Income' : 'Expense' }} Category</span>
        </span>
        <button type="button" class="btn-close-sm btn p-0 text-muted" @click="showInlineCreate = false">
          <X :size="14" />
        </button>
      </div>

      <div class="row g-2 mb-2">
        <div class="col-8">
          <input
            v-model="newCatName"
            type="text"
            class="form-control form-control-sm"
            placeholder="e.g. Coffee & Cafes, Subscriptions"
            autofocus
            @keydown.enter.prevent="createCategory"
          />
        </div>
        <div class="col-4">
          <button
            type="button"
            class="btn btn-primary btn-sm w-100 fw-semibold text-xs rounded-3 d-flex align-items-center justify-content-center gap-1"
            :disabled="creating || !newCatName.trim()"
            @click="createCategory"
          >
            <span v-if="creating" class="spinner-border spinner-border-sm" role="status"></span>
            <Check v-else :size="13" />
            <span>{{ creating ? 'Saving...' : 'Add' }}</span>
          </button>
        </div>
      </div>

      <!-- Color Swatch Selector -->
      <div class="d-flex align-items-center gap-1.5 flex-wrap">
        <span class="text-xxs text-muted me-1">Color:</span>
        <button
          v-for="color in colorPresets"
          :key="color"
          type="button"
          class="color-dot rounded-circle border-0 transition-transform"
          :style="{ backgroundColor: color }"
          :class="{ 'active-color': newCatColor === color }"
          @click="newCatColor = color"
        ></button>
      </div>
    </div>

    <!-- VISUAL CATEGORY CHIPS GRID -->
    <div v-if="displayCategories.length > 0" class="category-chips-grid">
      <button
        v-for="cat in displayCategories"
        :key="cat.id"
        type="button"
        class="category-chip-btn transition-all"
        :class="{ 'active-chip': modelValue === cat.id }"
        @click="selectCategory(cat.id)"
      >
        <!-- Color Icon Badge -->
        <span
          class="chip-icon-badge rounded-circle d-flex align-items-center justify-content-center"
          :style="{ backgroundColor: cat.color || defaultColor(cat.type) }"
        >
          <component :is="resolveIcon(cat.icon || cat.name)" :size="12" class="text-white" />
        </span>

        <!-- Category Name -->
        <span class="chip-name text-truncate">{{ cat.name }}</span>

        <!-- Active Checkmark Indicator -->
        <span v-if="modelValue === cat.id" class="chip-check text-primary ms-auto">
          <Check :size="13" />
        </span>
      </button>
    </div>

    <!-- Empty State / No Match -->
    <div v-else class="empty-category-notice text-center p-3 rounded-3 bg-light border text-muted">
      <Tag :size="20" class="opacity-50 mb-1" />
      <p class="text-xs mb-1">
        {{ searchQuery ? `No categories matching "${searchQuery}"` : `No ${type} categories found` }}
      </p>
      <button
        type="button"
        class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 text-xxs fw-semibold mt-1"
        @click="openInlineWithSearch"
      >
        + Add "{{ searchQuery || 'Category' }}"
      </button>
    </div>

    <!-- Hidden validation anchor for required forms -->
    <input
      type="text"
      class="visually-hidden"
      :value="modelValue"
      required
      tabindex="-1"
    />
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import {
  Tag,
  Search,
  Plus,
  X,
  Check,
  Sparkles,
  ShoppingCart,
  Utensils,
  Home,
  Zap,
  Car,
  Heart,
  BookOpen,
  Film,
  ShoppingBag,
  User,
  Shield,
  CreditCard,
  MoreHorizontal,
  Wallet,
  Building,
  Briefcase,
  Laptop,
  TrendingUp,
  Gift,
  Coffee,
  Plane,
  Smartphone,
  Flame,
} from '@lucide/vue';
import AxiosHelper from '@/libs/AppsbdAxiosHelper.js';
import AppsbdURL from '@/libs/AppsbdURL.js';
import AppsbdUtls from '@/libs/AppsbdUtls.js';

const props = defineProps({
  modelValue: {
    type: [Number, String, null],
    default: null,
  },
  type: {
    type: String,
    default: 'expense', // 'expense' or 'income'
  },
  categories: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['update:modelValue', 'category-created']);

const showSearch = ref(false);
const searchQuery = ref('');
const searchInputRef = ref(null);

const showInlineCreate = ref(false);
const newCatName = ref('');
const newCatColor = ref('#10b981');
const creating = ref(false);

const colorPresets = [
  '#ef4444', // Red
  '#f97316', // Orange
  '#f59e0b', // Amber
  '#10b981', // Emerald
  '#06b6d4', // Cyan
  '#3b82f6', // Blue
  '#8b5cf6', // Purple
  '#ec4899', // Pink
];

const filteredCategories = computed(() => {
  return props.categories.filter((c) => {
    if (!props.type || props.type === 'all') return true;
    return c.type === props.type;
  });
});

const displayCategories = computed(() => {
  if (!searchQuery.value.trim()) {
    return filteredCategories.value;
  }
  const q = searchQuery.value.toLowerCase();
  return filteredCategories.value.filter((c) =>
    c.name.toLowerCase().includes(q)
  );
});

function defaultColor(catType) {
  return catType === 'income' ? '#10b981' : '#6366f1';
}

function resolveIcon(iconNameOrTitle) {
  if (!iconNameOrTitle) return Tag;
  const lower = String(iconNameOrTitle).toLowerCase();

  if (lower.includes('food') || lower.includes('eat') || lower.includes('restaurant') || lower.includes('grocer')) return Utensils;
  if (lower.includes('coffee') || lower.includes('tea') || lower.includes('cafe')) return Coffee;
  if (lower.includes('home') || lower.includes('rent') || lower.includes('housing')) return Home;
  if (lower.includes('util') || lower.includes('bill') || lower.includes('electric') || lower.includes('power')) return Zap;
  if (lower.includes('car') || lower.includes('transport') || lower.includes('ride') || lower.includes('uber')) return Car;
  if (lower.includes('travel') || lower.includes('flight') || lower.includes('tour')) return Plane;
  if (lower.includes('health') || lower.includes('med') || lower.includes('doctor')) return Heart;
  if (lower.includes('edu') || lower.includes('course') || lower.includes('book')) return BookOpen;
  if (lower.includes('movie') || lower.includes('entertain') || lower.includes('film') || lower.includes('fun')) return Film;
  if (lower.includes('shop') || lower.includes('cloth') || lower.includes('bag')) return ShoppingBag;
  if (lower.includes('cart') || lower.includes('market')) return ShoppingCart;
  if (lower.includes('salary') || lower.includes('wage')) return Wallet;
  if (lower.includes('business') || lower.includes('company')) return Building;
  if (lower.includes('free') || lower.includes('remote') || lower.includes('tech') || lower.includes('laptop')) return Laptop;
  if (lower.includes('invest') || lower.includes('stock') || lower.includes('profit') || lower.includes('crypto')) return TrendingUp;
  if (lower.includes('gift') || lower.includes('bonus') || lower.includes('reward')) return Gift;
  if (lower.includes('phone') || lower.includes('mobile') || lower.includes('recharge')) return Smartphone;
  if (lower.includes('debt') || lower.includes('loan') || lower.includes('card')) return CreditCard;
  if (lower.includes('person') || lower.includes('self')) return User;
  if (lower.includes('insur') || lower.includes('safe')) return Shield;

  return Tag;
}

function selectCategory(id) {
  emit('update:modelValue', id);
}

function toggleSearch() {
  showSearch.value = !showSearch.value;
  if (showSearch.value) {
    nextTick(() => {
      searchInputRef.value?.focus();
    });
  } else {
    searchQuery.value = '';
  }
}

function openInlineWithSearch() {
  newCatName.value = searchQuery.value;
  newCatColor.value = props.type === 'income' ? '#10b981' : '#ef4444';
  showInlineCreate.value = true;
}

async function createCategory() {
  if (!newCatName.value.trim()) return;
  try {
    creating.value = true;
    const payload = {
      name: newCatName.value.trim(),
      type: props.type || 'expense',
      color: newCatColor.value,
      icon: 'tag',
      is_active: true,
    };

    const res = await AxiosHelper.post(AppsbdURL.route('categories'), payload);
    if (res?.status && res?.data) {
      AppsbdUtls.ShowServerResponseNotification(res.msg || 'Category created', 3000);
      emit('category-created', res.data);
      emit('update:modelValue', res.data.id);
      newCatName.value = '';
      showInlineCreate.value = false;
      searchQuery.value = '';
      showSearch.value = false;
    }
  } catch (e) {
    console.error('Failed to create category', e);
  } finally {
    creating.value = false;
  }
}
</script>

<style scoped lang="scss">
.category-picker-wrapper {
  position: relative;
}

.btn-light-primary {
  background-color: #f0fdf4;
  color: #137035;
  border: 1px solid #bbf7d0;

  &:hover {
    background-color: #dcfce7;
    color: #166534;
  }
}

.category-chips-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
  gap: 0.5rem;
  max-height: 220px;
  overflow-y: auto;
  padding: 2px;

  &::-webkit-scrollbar {
    width: 4px;
  }
  &::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
}

.category-chip-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.45rem 0.65rem;
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 0.65rem;
  font-size: 0.8125rem;
  font-weight: 500;
  color: #334155;
  text-align: left;
  cursor: pointer;
  width: 100%;

  &:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
  }

  &.active-chip {
    background: #f0fdf4;
    border-color: #137035;
    color: #137035;
    font-weight: 600;
    box-shadow: 0 0 0 1px #137035, 0 2px 4px rgba(19, 112, 53, 0.08);
  }
}

.chip-icon-badge {
  width: 22px;
  height: 22px;
  flex-shrink: 0;
}

.chip-name {
  font-size: 0.775rem;
  line-height: 1.2;
}

.color-dot {
  width: 18px;
  height: 18px;
  cursor: pointer;

  &:hover {
    transform: scale(1.2);
  }

  &.active-color {
    box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px #137035;
    transform: scale(1.15);
  }
}

.inline-creator-card {
  border-color: #cbd5e1 !important;
}

.animate-fade-in {
  animation: fadeIn 0.15s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.text-xxs {
  font-size: 0.6875rem;
}
</style>
