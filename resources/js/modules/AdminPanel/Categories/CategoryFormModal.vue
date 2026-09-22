<template>
  <div v-if="modelValue" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3" @click.self="close">
    <div class="modal-card bg-white rounded-4 shadow-xl border-0 overflow-hidden animate-scale-in" style="max-width: 480px; width: 100%;">
      
      <!-- Modal Header (Fixed) -->
      <div class="modal-header-custom d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
          <div
            class="header-icon-pill rounded-3 p-2.5 d-flex align-items-center justify-content-center"
            :style="{ backgroundColor: form.color + '20', color: form.color }"
          >
            <component :is="editData ? Pencil : Plus" :size="20" />
          </div>
          <div>
            <h5 class="fw-bold mb-0 text-dark">{{ editData ? 'Edit Category' : 'New Category' }}</h5>
            <p class="text-muted text-xs mb-0">
              {{ editData ? 'Update category details, icon and color' : 'Create a custom category for income or expenses' }}
            </p>
          </div>
        </div>
        <button type="button" class="btn btn-icon btn-light rounded-circle p-2" @click="close" aria-label="Close">
          <X :size="18" class="text-muted" />
        </button>
      </div>

      <!-- Modal Body (Scrollable with max height) -->
      <div class="modal-body-scroll px-4 py-3">
        <!-- Live Visual Preview Card -->
        <div class="mb-3">
          <div
            class="preview-box p-3 rounded-4 d-flex align-items-center gap-3 border transition-all shadow-xs"
            :class="form.type === 'income' ? 'preview-income' : 'preview-expense'"
          >
            <div
              class="preview-icon-badge rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0 shadow-xs"
              :style="{ backgroundColor: form.color || '#6366f1' }"
            >
              <component :is="selectedIconComponent" :size="18" />
            </div>
            <div class="min-w-0 flex-grow-1">
              <h6 class="fw-bold mb-1 text-dark text-truncate">
                {{ form.name || 'Category Name' }}
              </h6>
              <div class="d-flex align-items-center gap-1.5">
                <span class="btn-action-compact rounded-circle p-0 d-inline-flex align-items-center justify-content-center opacity-75">
                  <Pencil :size="11" class="text-secondary" />
                </span>
                <span class="btn-action-compact rounded-circle p-0 d-inline-flex align-items-center justify-content-center opacity-75 text-danger">
                  <Trash2 :size="11" />
                </span>
              </div>
            </div>
            <span class="text-xxs text-muted fw-semibold text-uppercase tracking-wider ms-auto">Preview</span>
          </div>
        </div>

        <!-- Category Name -->
        <div class="mb-3">
          <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
            Category Name <span class="text-danger">*</span>
          </label>
          <input
            ref="nameInputRef"
            v-model="form.name"
            type="text"
            class="form-control form-control-modern"
            placeholder="e.g. Groceries, Freelance, Rent"
            required
            maxlength="100"
            @input="handleNameInput"
            @keydown.enter.prevent="handleSubmit"
          />
        </div>

        <!-- Type Selector (Segmented Button Group) -->
        <div class="mb-3">
          <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-1.5">
            Category Type <span class="text-danger">*</span>
          </label>
          <div class="type-selector-group p-1 bg-light rounded-3 d-flex gap-1 border">
            <button
              type="button"
              class="type-btn flex-fill py-2 rounded-2 border-0 fw-semibold text-xs transition-all d-flex align-items-center justify-content-center gap-1.5"
              :class="{ 'active-expense': form.type === 'expense' }"
              @click="setType('expense')"
            >
              <span>Expense</span>
            </button>
            <button
              type="button"
              class="type-btn flex-fill py-2 rounded-2 border-0 fw-semibold text-xs transition-all d-flex align-items-center justify-content-center gap-1.5"
              :class="{ 'active-income': form.type === 'income' }"
              @click="setType('income')"
            >
              <span>Income</span>
            </button>
          </div>
        </div>

        <!-- Color Palette Picker with extra bottom margin -->
        <div class="mb-3.5">
          <div class="d-flex align-items-center justify-content-between mb-2.5">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-0">
              Badge Color
            </label>
            <span class="badge bg-light text-muted border font-monospace text-xxs px-2 py-0.5">{{ form.color }}</span>
          </div>
          
          <!-- Single non-wrapping row for colors + custom picker -->
          <div class="color-palette-row">
            <button
              v-for="color in presetColors"
              :key="color"
              type="button"
              class="color-circle-btn rounded-circle border-0 transition-transform"
              :style="{ backgroundColor: color }"
              :class="{ 'active-color': form.color.toLowerCase() === color.toLowerCase() }"
              @click="form.color = color"
              :title="color"
            ></button>

            <!-- Custom Color Input: always on the same line -->
            <label class="color-custom-label rounded-circle border d-flex align-items-center justify-content-center cursor-pointer mb-0 position-relative" title="Pick custom color">
              <input
                v-model="form.color"
                type="color"
                class="position-absolute opacity-0"
                style="width: 100%; height: 100%; cursor: pointer;"
              />
              <span class="text-xs fw-bold text-muted lh-1">+</span>
            </label>
          </div>
        </div>

        <!-- Clean, Modern Icon Picker with 77+ Icons -->
        <div class="mb-2">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <label class="form-label text-xs fw-bold text-uppercase tracking-wider text-muted mb-0">
              Choose Icon <span class="text-danger">*</span>
            </label>
            <span class="text-xs fw-semibold text-dark d-flex align-items-center gap-1.5">
              <span
                class="rounded-circle d-inline-flex align-items-center justify-content-center text-white"
                :style="{ backgroundColor: form.color, width: '18px', height: '18px' }"
              >
                <component :is="selectedIconComponent" :size="11" />
              </span>
              <span>{{ activeIconLabel }}</span>
            </span>
          </div>

          <!-- Quick Category Filter Pills -->
          <div class="icon-group-pills d-flex align-items-center gap-1 overflow-x-auto pb-1 mb-2">
            <button
              v-for="grp in ['All', 'Food & Drinks', 'Shopping', 'Income', 'Finance', 'Housing', 'Transport', 'Health', 'Education', 'Entertainment', 'Personal']"
              :key="grp"
              type="button"
              class="btn btn-xs rounded-pill px-2.5 py-0.5 text-xxs transition-all flex-shrink-0"
              :class="activeGroup === grp ? 'btn-primary shadow-xs' : 'btn-light border text-muted'"
              @click="activeGroup = grp"
            >
              {{ grp }}
            </button>
          </div>

          <!-- Search Input for Icons -->
          <div class="input-group input-group-sm rounded-pill overflow-hidden border bg-light mb-2">
            <span class="input-group-text bg-transparent border-0 text-muted ps-3 pe-1">
              <Search :size="13" />
            </span>
            <input
              v-model="iconSearchQuery"
              type="text"
              class="form-control bg-transparent border-0 ps-1 text-xs py-1"
              placeholder="Search 77+ icons (e.g. food, drinks, coffee, car, salary)..."
            />
            <button
              v-if="iconSearchQuery"
              type="button"
              class="btn btn-link btn-sm text-muted pe-3 text-decoration-none"
              @click="iconSearchQuery = ''"
            >
              <X :size="12" />
            </button>
          </div>

          <!-- Clean Icon Grid without clunky text labels -->
          <div class="icon-selector-grid border rounded-3 p-2 bg-light">
            <button
              v-for="iconItem in filteredIcons"
              :key="iconItem.id"
              type="button"
              class="icon-grid-tile rounded-3 border-0 d-flex align-items-center justify-content-center transition-all"
              :class="{ 'is-selected': form.icon === iconItem.id }"
              :style="form.icon === iconItem.id ? { backgroundColor: form.color, color: '#ffffff' } : {}"
              @click="form.icon = iconItem.id"
              :title="iconItem.name"
            >
              <component :is="iconItem.component" :size="18" />
            </button>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-1.5 px-1">
            <span class="text-xxs text-muted">{{ filteredIcons.length }} icons available</span>
            <span v-if="iconSearchQuery || activeGroup !== 'All'" class="text-xxs text-primary cursor-pointer" @click="resetIconFilters">
              Reset filter
            </span>
          </div>
        </div>
      </div>

      <!-- Modal Footer (Fixed at bottom so buttons are always visible) -->
      <div class="modal-footer-custom px-4 py-3 border-top bg-light bg-opacity-50 d-flex align-items-center justify-content-end gap-2">
        <ab-button type="button" color="light" size="sm" @click="close">
          Cancel
        </ab-button>
        <ab-button
          type="button"
          color="primary"
          size="sm"
          :is-animated="saving"
          :disabled="saving || !form.name.trim()"
          @click="handleSubmit"
        >
          {{ editData ? 'Save Changes' : 'Create Category' }}
        </ab-button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import {
  X,
  Plus,
  Pencil,
  Trash2,
  Search,
} from '@lucide/vue';
import {
  CATEGORY_ICON_LIST,
  resolveCategoryIcon,
} from '@/libs/CategoryIcons.js';

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

const nameInputRef = ref(null);
const iconSearchQuery = ref('');
const activeGroup = ref('All');

const presetColors = [
  '#ef4444', // Red
  '#f97316', // Orange
  '#f59e0b', // Amber
  '#10b981', // Emerald
  '#14b8a6', // Teal
  '#06b6d4', // Cyan
  '#3b82f6', // Blue
  '#6366f1', // Indigo
  '#8b5cf6', // Violet
  '#d946ef', // Fuchsia
  '#ec4899', // Pink
  '#64748b', // Slate
];

const form = ref({
  id: null,
  name: '',
  type: 'expense',
  color: '#ef4444',
  icon: 'shopping-cart',
  is_active: true,
});

const filteredIcons = computed(() => {
  let list = CATEGORY_ICON_LIST;
  if (activeGroup.value !== 'All') {
    list = list.filter((i) => i.category === activeGroup.value);
  }
  if (iconSearchQuery.value.trim()) {
    const q = iconSearchQuery.value.toLowerCase().trim();
    list = list.filter(
      (i) =>
        i.name.toLowerCase().includes(q) ||
        i.id.toLowerCase().includes(q) ||
        (i.keywords && i.keywords.some((k) => k.includes(q)))
    );
  }
  return list;
});

function resetIconFilters() {
  iconSearchQuery.value = '';
  activeGroup.value = 'All';
}

const selectedIconComponent = computed(() => {
  return resolveCategoryIcon(form.value.icon || form.value.name);
});

const activeIconLabel = computed(() => {
  const found = CATEGORY_ICON_LIST.find((i) => i.id === form.value.icon);
  return found ? found.name : form.value.icon;
});

// Sync form with editData when modal opens
watch(
  () => props.modelValue,
  (val) => {
    if (val) {
      iconSearchQuery.value = '';
      activeGroup.value = 'All';
      if (props.editData) {
        form.value = {
          id: props.editData.id,
          name: props.editData.name || '',
          type: props.editData.type || 'expense',
          color: props.editData.color || '#6366f1',
          icon: props.editData.icon || 'tag',
          is_active: props.editData.is_active ?? true,
        };
      } else {
        form.value = {
          id: null,
          name: '',
          type: 'expense',
          color: '#ef4444',
          icon: 'shopping-cart',
          is_active: true,
        };
      }
      nextTick(() => {
        nameInputRef.value?.focus();
      });
    }
  }
);

function setType(type) {
  form.value.type = type;
  // If creating new and color matches default, toggle to an appropriate preset color
  if (!props.editData) {
    if (type === 'income' && form.value.color === '#ef4444') {
      form.value.color = '#10b981';
      form.value.icon = 'wallet';
    } else if (type === 'expense' && form.value.color === '#10b981') {
      form.value.color = '#ef4444';
      form.value.icon = 'shopping-cart';
    }
  }
}

function handleNameInput() {
  // Only auto-suggest icon on create if user hasn't explicitly chosen one yet
  if (!props.editData && form.value.name.length > 2) {
    const raw = form.value.name.toLowerCase();
    for (const item of CATEGORY_ICON_LIST) {
      if (item.keywords.some((kw) => raw.includes(kw))) {
        form.value.icon = item.id;
        break;
      }
    }
  }
}

function close() {
  emit('update:modelValue', false);
}

function handleSubmit() {
  if (!form.value.name.trim()) return;
  emit('save', {
    ...form.value,
    name: form.value.name.trim(),
  });
}
</script>

<style scoped lang="scss">
.modal-backdrop-custom {
  position: fixed;
  inset: 0;
  background-color: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(4px);
  z-index: 1055;
}

.modal-card {
  max-height: 88vh;
  display: flex;
  flex-direction: column;
}

.modal-body-scroll {
  overflow-y: auto;
  max-height: calc(88vh - 135px);

  &::-webkit-scrollbar {
    width: 5px;
  }
  &::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
}

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

.header-icon-pill {
  width: 42px;
  height: 42px;
}

.preview-box {
  transition: all 0.2s ease;

  &.preview-expense {
    background: linear-gradient(135deg, #fff7f7 0%, #ffffff 100%);
    border-color: #fee2e2 !important;
  }

  &.preview-income {
    background: linear-gradient(135deg, #f4fbf7 0%, #ffffff 100%);
    border-color: #d1fae5 !important;
  }
}

.btn-action-compact {
  width: 22px;
  height: 22px;
  background-color: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}

.preview-icon-badge {
  width: 38px;
  height: 38px;
}

.form-control-modern {
  border-radius: 0.65rem;
  padding: 0.55rem 0.85rem;
  font-size: 0.875rem;
  border-color: #cbd5e1;

  &:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
  }
}

.type-selector-group {
  .type-btn {
    background: transparent;
    color: #64748b;

    &:hover {
      color: #1e293b;
    }

    &.active-expense {
      background: #ffffff;
      color: #ef4444;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    &.active-income {
      background: #ffffff;
      color: #10b981;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }
  }
}

.color-palette-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  flex-wrap: nowrap;
}

.color-circle-btn {
  width: 22px;
  height: 22px;
  min-width: 22px;
  flex-shrink: 0;
  cursor: pointer;

  &:hover {
    transform: scale(1.2);
  }

  &.active-color {
    box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px #0f172a;
    transform: scale(1.1);
  }
}

.color-custom-label {
  width: 22px;
  height: 22px;
  min-width: 22px;
  flex-shrink: 0;
  background: #f1f5f9;
  border-color: #cbd5e1 !important;

  &:hover {
    background: #e2e8f0;
  }
}

.icon-group-pills {
  &::-webkit-scrollbar {
    height: 3px;
  }
  &::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
}

.icon-selector-grid {
  display: grid;
  grid-template-columns: repeat(8, 1fr);
  gap: 6px;
  max-height: 160px;
  overflow-y: auto;

  &::-webkit-scrollbar {
    width: 4px;
  }
  &::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
}

.icon-grid-tile {
  height: 38px;
  background: #ffffff;
  color: #475569;
  cursor: pointer;

  &:hover {
    background: #e2e8f0;
    color: #0f172a;
    transform: translateY(-1px);
  }

  &.is-selected {
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
  }
}

.text-xxs {
  font-size: 0.6875rem;
}

[data-bs-theme="dark"] {
  .modal-card {
    background-color: #1e293b !important;
  }
  .modal-footer-custom {
    background-color: #0f172a !important;
    border-color: #334155 !important;
  }
  .preview-box {
    &.preview-expense {
      background: rgba(239, 68, 68, 0.08) !important;
      border-color: rgba(239, 68, 68, 0.25) !important;
    }
    &.preview-income {
      background: rgba(16, 185, 129, 0.08) !important;
      border-color: rgba(16, 185, 129, 0.25) !important;
    }
  }
  .type-selector-group {
    background-color: #0f172a !important;
    border-color: #334155 !important;

    .type-btn {
      color: #94a3b8;
      &.active-expense {
        background: #1e293b;
        color: #f87171;
      }
      &.active-income {
        background: #1e293b;
        color: #34d399;
      }
    }
  }
  .form-control-modern {
    background-color: #0f172a;
    border-color: #334155;
    color: #f8fafc;
  }
  .icon-selector-grid {
    background-color: #0f172a;
    border-color: #334155;
  }
  .icon-grid-tile {
    background: #1e293b;
    color: #cbd5e1;
    &:hover {
      background: #334155;
      color: #ffffff;
    }
  }
}
</style>
