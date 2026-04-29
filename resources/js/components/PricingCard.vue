<template>
    <div class="row row-cols-1 row-cols-md-3 g-3 pricing-wrapper">
        <div v-for="pkg in packages" :key="pkg.id" class="col">
            <div class="card pricing-card h-100">
                <div class="card-body d-flex flex-column text-center">

                    <!-- Package title + type -->
                    <div class="package-title">
                        <h5 class="pkg-name">
                            {{ appsbdUtls.translateGettext(pkg.name) }}
                            <translate>gbl.package</translate>
                            /  <span class="pkg-type">
                                 {{ getType(pkg) }}
                               </span>
                        </h5>

                    </div>

                    <!-- Monthly price -->
                    <div class="pricing-price">
                        <span class="amount">
                            {{ AppHelper.formatCurrency(pkg.price) }}
                        </span>
                        <span class="period">
                            /{{ appsbdUtls.translateGettext('month') }}
                        </span>
                    </div>

                    <!-- Yearly price (bigger & highlighted) -->
                    <div v-if="pkg.type === 'Y'" class="yearly-price">
                        <translate>Every year</translate>:
                        <span class="yearly-amount">
                            {{ AppHelper.formatCurrency(calculatePrice(pkg)) }}
                        </span>
                    </div>

                    <!-- Features -->
                    <ul class="list-unstyled text-start flex-grow-1 pricing-features">
                        <li
                            v-for="f in pkg.features"
                            :key="f.key"
                            class="feature-item"
                        >
                            <span class="dot"></span>

                            <span class="feature-text" v-translate>
                                {{ f.title }}
                            </span>

                            <span class="feature-value">
                                <template v-if="f.val_type !== 'B'">
                                    {{ AppHelper.formatAmount(f.prop_val) }}
                                </template>
                                <template v-else>
                                    {{ getVal(f.prop_val) }}
                                </template>
                            </span>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import AppHelper from "../libs/AppHelper.js";
import appsbdUtls from "../libs/AppsbdUtls.js";

defineProps({
    packages: {
        type: Array,
        required: true
    }
});

const getType = (pkg) =>
    pkg.type === 'Y'
        ? appsbdUtls.translateGettext('gbl.yearly')
        : appsbdUtls.translateGettext('gbl.monthly');

const calculatePrice = (pkg) =>
    pkg.type === 'Y' ? pkg.price * 12 : pkg.price;

const getVal = (val) =>
    val === 'Y'
        ? appsbdUtls.translateGettext('Yes')
        : appsbdUtls.translateGettext('No');
</script>


<style scoped lang="scss">
.pricing-wrapper {
    margin-top: 0.5rem;
}

/* Card */
.pricing-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    transition: all 0.25s ease;
    background: var(--ab-card-bg);
}

.pricing-card:hover {
    border-color: var(--ab-theme-color, #137035);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.07);
}

/* Package title */
.package-title {
    margin-bottom: 0.25rem;
}

.pkg-name {
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: 2px;
}

.pkg-type {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--ab-theme-color, #137035);
}

/* Monthly price */

.pricing-price .amount {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--ab-theme-color, #137035);
}

.pricing-price .period {
    font-size: 0.9rem;
    color: var(--ab-body-color);
    margin-left: 4px;
}

/* Yearly price highlight */
.yearly-price {

    font-size: 1rem;
    color: var(--ab-body-color);
}

.yearly-amount {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--ab-body-color);
    margin-left: 4px;
}

/* Features */
.pricing-features {
    margin-top: 0.6rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.45rem;
    font-size: 0.92rem;
}

.feature-text {
    flex: 1;
}

.feature-value {
    font-weight: 500;
    color: var(--ab-body-color);
}

/* Dot icon */
.dot {
    width: 6px;
    height: 6px;
    background: var(--ab-theme-color,#137035);
    border-radius: 50%;
}
</style>




