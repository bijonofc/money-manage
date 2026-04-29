<template>
    <div class="">
        <app-loader msg="dashboard.loading" v-if="isShowLoader" />
        <div v-else>
            <div class="row row-cols-1">
                <div class="col">
                    <div class="card">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="title text-muted">👋 Welcome back, {{ loginStore.loggedUserData.name }}</h4>
                                <p class="subtitle mb-0">Here’s a quick overview of your NogorPOS system.</p>
                            </div>

                            <button v-if="$CheckACL('np.dashboard-stat')" class="btn btn-sm btn-primary" @click="load">
                                <i class="apb apb-refresh-ccw-alt"> </i>
                                <span class="ms-2" v-translate>gbl.reload</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <PricingCard :packages="d.packages" />
        </div>



    </div>
</template>

<script setup>
import {ref, computed, onMounted} from "vue";
import { AppLoader } from "@appsbd/vue3-appsbd-libs";
import { useDashboardStore } from "@/modules/AdminPanel/Dashboard/DashboardStore.js";
import { useLoginStore } from "@/modules/AdminPanel/User/loginStore.js";

// Components
import StatBox from "@/components/StatBox.vue";
import SectionTitle from "@/components/SectionTitle.vue";
import appHelper from "@/libs/AppHelper.js";
import PricingCard from "@/components/PricingCard.vue";

const dashboardStore = useDashboardStore();
const loginStore = useLoginStore();
const isShowLoader = ref(false);

const d = computed(() => dashboardStore.initialData ?? {});

async function load() {
    if (!loginStore.isLoggedIn) return;
    try {
        isShowLoader.value = true;
        const res=await dashboardStore.getInitialData();
    }catch (e) {
       console.log(e);
    }
    isShowLoader.value = false;
}

onMounted(load);
</script>

<style scoped lang="scss">
.package-list {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 6px;
}

.package-row {
    display: flex;
    gap:4px;
    align-items: center;
    padding: 8px 12px;
    border-radius: 8px;
    background: var(--ab-card-bg);
    border: 1px solid #e5e7eb;
    font-size: 14px;
}

.package-info {
    display: flex;
    align-items: center;
    font-weight: 500;
}

.pkg-count {
    font-weight: 600;
    color: var(--ab-main-color);
}
</style>
