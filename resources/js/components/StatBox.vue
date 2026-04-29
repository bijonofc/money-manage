<template>
    <div class="col-xl-3 col-md-6">
        <div class="stat-box d-flex align-items-center gap-2" :class="colorClass">
            <div class="icon-wrap">
                <i :class="['apb', icon]"></i>
            </div>
            <div>
                <p class="label" v-translate>{{ label }}</p>
                <div class="d-flex align-items-center gap-1 value" >

                    <span v-if="needTkIcon">
                        {{ appHelper.formatCurrency(value) }}
                    </span>
                    <span v-else>
                         {{appHelper.toLocalizedDigits(value)}}
                    </span>
                </div>
            </div>
            <slot name="action"></slot>

        </div>
    </div>
</template>

<script setup>
import {computed} from "vue";
import appHelper from "@/libs/AppHelper.js";

const props=defineProps({
    label: String,
    value: [String, Number],
    icon: String,
    color: String,
    needTkIcon: {
        type: Boolean,
        default: false
    }

});

const colorClass = computed(() => `box-${props.color}`);
</script>

<style scoped>
.stat-box {
    background: var(--ab-card-bg);
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transition: 0.25s;
}
.stat-box:hover {
    transform: translateY(-6px);
}
.icon-wrap {
    font-size: 26px;
    color: #fff;
    display: inline-flex;
    padding: 12px;
    border-radius: 12px;
    margin-bottom: 12px;
}
.label {
    color: var(--ab-body-color);
    margin: 0;
}
.value {
    font-size: 24px;
    font-weight: bold;
}

/* Color styles */
.box-blue .icon-wrap { background: #4e73df; }
.box-green .icon-wrap { background: #1cc88a; }
.box-yellow .icon-wrap { background: #f6c23e; }
.box-red .icon-wrap { background: #e74a3b; }
.box-purple .icon-wrap { background: #6f42c1; }
.box-orange .icon-wrap { background: #fd7e14; }
</style>
