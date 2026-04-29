<template>
    <div>
        <app-loader msg="com.load" v-if="isShowLoader"/>

        <div v-else>
            <div class="card mb-4">
                <div class="card-body d-flex flex-sm-row flex-column justify-content-between align-items-start align-items-sm-center">
                    <div>
                        <h4 class="fw-bold">🏢  Dashboard</h4>
                        <p class="text-muted mb-0" v-translate>gbl.company.overview</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">

                        <button v-if="$CheckACL('np.company-dashboard')" class="btn btn-sm btn-primary" @click="reload">
                            <i class="apb apb-refresh-ccw-alt me-1"></i>
                            <translate>gbl.reload</translate>
                        </button>
                    </div>

                </div>
            </div>
            <div class="row row-cols-1 mb-4">
                <div class="col">
                    <div class="card shadow-sm p-3 company-card">

                        <h5 class="mb-3 fw-bold" v-translate>
                            gbl.company.details
                        </h5>

                        <div class="row g-4">

                            <!-- Company Name -->
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <div class="icon"><i class="apb apb-briefcase"></i></div>
                                    <div>
                                        <p class="label" v-translate>gbl.company.name</p>
                                        <p class="value">{{ companyData?.company?.name }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <div class="icon"><i class="apb apb-mail"></i></div>
                                    <div>
                                        <p class="label" v-translate>gbl.company.email</p>
                                        <p class="value">{{ companyData?.company?.email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <div class="icon"><i class="apb apb-phone-call"></i></div>
                                    <div>
                                        <p class="label" v-translate>gbl.company.phone</p>
                                        <p class="value">{{ companyData?.company?.phone }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <div class="icon"><i class="apb apb-map-pin"></i></div>
                                    <div>
                                        <p class="label" v-translate>gbl.company.address</p>
                                        <p class="value">{{ companyData?.company?.address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Boxes -->
            <div class="row g-4">

                <StatBox label="gbl.balance" :need-tk-icon="true" :value="(companyData?.company?.credit-companyData?.company?.debit)" icon="apb-dollar-sign"  color="green"/>
                <StatBox label="gbl.credit" :need-tk-icon="true" :value="companyData?.company?.credit" icon="apb-dollar-sign"  color="red"/>
                <StatBox label="gbl.debit" :need-tk-icon="true" :value="companyData?.company?.debit" icon="apb-dollar-sign"  color="blue"/>
<!--                <StatBox label="gbl.staff.credit" :value="company.total_staff_credit" icon="apb-dollar-sign" :need-tk-icon="true" color="green" />-->

                <section-title icon="apb-users-01" title="customer.summary" />
                <div class="row ">
                    <StatBox label="total.temp.customers"  :value="companyData?.customers?.total_temp" icon="apb-user-plus" color="blue"/>
                    <StatBox label="total.customers" :value="companyData?.customers?.total_users" icon="apb-user-plus" color="green"/>
                    <StatBox label="today.temp.customers" :value="companyData?.customers?.today_temp" icon="apb-user-plus" color="yellow"/>
                    <StatBox label="today.customers" :value="companyData?.customers?.today_users" icon="apb-user-plus" color="red"/>
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <div class="card  p-3 mb-4">
                            <section-title icon="apb-users-01" title="package.wise.customers" />
                            <div v-if="companyData?.packages?.package_wise?.length" class="package-list">
                                <div v-for="pkg in companyData?.packages?.package_wise" :key="pkg.id" class="package-row">
                                    <div class="package-info">
                                        <strong>{{ pkg.name }}</strong>
                                        <span class="text-muted ms-1">({{ appHelper.toLocalizedDigits(pkg.price,'bn') }}<translate>gbl.tk</translate>)</span>
                                    </div>
                                    <div class="pkg-count">{{ appHelper.toLocalizedDigits(pkg.total_users,'bn') }}</div>
                                </div>
                            </div>
                            <div v-else class="text-danger text-center py-3" v-translate>no.pkg</div>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </div>

</template>

<script setup>
import {ref, onMounted, reactive} from "vue";
import {useCompanyStore} from "@/modules/AdminPanel/Company/CompanyStore.js";
import {AppLoader} from "@appsbd/vue3-appsbd-libs";
import StatBox from "@/components/StatBox.vue";
import appHelper from "@/libs/AppHelper.js";
import AppHelper from "@/libs/AppHelper.js";
import SectionTitle from "@/components/SectionTitle.vue";



const companyStore = useCompanyStore();
const isShowLoader = ref(false);
const isShowModal = ref(false);
const companyId = ref(null);

// Single company object
const companyData=reactive({});

// Fetch company data
const loadData = async () => {
    try {
        isShowLoader.value = true;

        const response = await companyStore.getCompanyData();

        if (response.status && response.data) {
            Object.assign(companyData,response.data)
        }
    } catch (e) {
        console.error(e);
    }

    isShowLoader.value = false;
};
function openLedgerModal() {
    companyId.value = company.value.id;
    isShowModal.value = true;
}
function closeModal() {
    isShowModal.value = false;
    companyId.value = null;
}
onMounted(loadData);

// Reload button
function reload() {
    loadData();
}
</script>

<style scoped lang="scss">
.stat-box {
    background: linear-gradient(180deg, #ffffff, #f9fafb);
    padding: 22px;
    border-radius: 18px;
    box-shadow: 0 10px 28px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    justify-content: space-between;
}
.stat-box .label {
    font-size: 22px;
    font-weight: 700;
    color: #6c757d;
    margin-bottom: 6px;
}

.stat-box .value {
    font-size: 18px;
    font-weight: 500;
    letter-spacing: 0.3px;
    color: #212529;
}
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
