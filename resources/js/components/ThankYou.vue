<template>
    <div class="reg-body">
        <Header v-if="showHeader"/>
        <div class="reg-content">
            <div class="container-fluid h-100">
                <div class="row">
                    <div class="col" v-if="isShowLoader">
                        <AppLoader v-if="isShowLoader"/>
                    </div>
                    <div v-else class="col p-0">
                        <div class="success-page d-flex align-items-center justify-content-center">
                            <div class="success-card d-flex flex-column flex-md-row shadow-lg">


                                <div class="visual-wrap d-none d-md-flex align-items-center justify-content-center">
                                    <div class="illustration">
                                        <div class="circle-outer">
                                            <svg viewBox="0 0 120 120" class="check-svg">
                                                <circle cx="60" cy="60" r="54" fill="none" />
                                                <path d="M34 62 L52 78 L86 42" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div class="confetti">
                                            <span class="c c1"></span>
                                            <span class="c c2"></span>
                                            <span class="c c3"></span>
                                            <span class="c c4"></span>
                                            <span class="c c5"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="content-wrap d-flex flex-column justify-content-center p-2 p-sm-4">

                                    <!-- Mobile Illustration (on top) -->
                                    <div class="mobile-illustration d-flex d-md-none justify-content-center mb-4">
                                        <div class="illustration">
                                            <div class="circle-outer">
                                                <svg viewBox="0 0 120 120" class="check-svg">
                                                    <circle cx="60" cy="60" r="54" fill="none" />
                                                    <path d="M34 62 L52 78 L86 42" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <div class="confetti">
                                                <span class="c c1"></span>
                                                <span class="c c2"></span>
                                                <span class="c c3"></span>
                                                <span class="c c4"></span>
                                                <span class="c c5"></span>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h3 class="mb-1" v-translate>gbl.payment.success</h3>
                                            <p class="text-muted mb-0" v-translate>gbl.success.msg</p>
                                        </div>

                                    </div>
                                    <div>
                                        <h6 v-translate>gbl.payment.details</h6>
                                    </div>

                                    <div class="details-section p-2 p-sm-3 mb-3 mb-sm-4">

                                        <div class="detail-row d-flex justify-content-between mb-2">
                                            <div class="detail-label" v-translate>gbl.trx.id</div>
                                            <div class="detail-value">{{ order?.invoice_number }}</div>
                                        </div>
                                        <div
                                            v-for="item in order.items"
                                            :key="item.id"
                                        >
                                            <div class="detail-row d-flex justify-content-between mb-2">
                                                <div class="detail-label" v-translate>gbl.pkg.name</div>
                                                <div class="detail-value">{{ item.name }}</div>
                                            </div>

                                            <div class="detail-row d-flex justify-content-between mb-2">
                                                <div class="detail-label" v-translate>gbl.pkg.price</div>
                                                <div class="detail-value fw-bold">
                                                    <span  style="font-size: 1rem;">৳</span>
                                                    {{appHelper.toLocalizedDigits(item.unit_price,'bn')}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="detail-row d-flex justify-content-between mb-2" v-if="order?.status=='S'">
                                            <div class="detail-label" v-translate>gbl.payment.date</div>
                                            <div class="detail-value">{{ appHelper.formatDateTime(order?.paid_at) }}</div>
                                        </div>
                                        <div class="detail-row d-flex justify-content-between mb-2" v-if="order?.status!='S'">
                                            <div class="detail-label" v-translate>gbl.due.date</div>
                                            <div class="detail-value">{{ appHelper.formatDateTime(order?.due_date) }}</div>
                                        </div>
                                        <div class="detail-row d-flex justify-content-between mb-2">
                                            <div class="detail-label" v-translate>gbl.payment.method</div>
                                            <div class="detail-value">Bkash</div>
                                        </div>
                                        <div class="detail-row d-flex justify-content-between mt-2">
                                            <div class="detail-label" v-translate>gbl.total.pay</div>
                                            <div class="detail-value fw-bold">
                                                <span  style="font-size: 1rem;">৳</span>
                                                {{appHelper.toLocalizedDigits(order?.total_amount,'bn')}}
                                            </div>
                                        </div>
                                        <div class="detail-row d-flex justify-content-between mt-2">
                                            <div class="detail-label" v-translate>gbl.status</div>
                                            <div class="detail-value fw-bold">
                                                <span class="text-success" v-if="order.status=='S'">
                                                    <i class="apb apb-circle-check "></i>
                                                    success
                                                </span>
                                                <span class="text-success" v-else>
                                                    <i class="apb apb-circle-check "></i>
                                                    Failed
                                                </span>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-sm-row gap-4 justify-content-between">
                                        <button class="btn btn-block btn-outline-primary d-flex justify-content-center align-items-center gap-2" @click="downloadInvoice">
                                            <i class="apb apb-eye-01"></i>
                                            <translate>view.invoice</translate>
                                            <small-loader v-if="isLoading" :show="isLoading" size="5" />
                                        </button>

                                        <a href="https://nogorpos.com/" target="_blank" class="btn btn-block justify-content-center btn-success d-flex align-items-center gap-2">
                                            <i class="apb apb-Home-01"></i>
                                            <translate>go.home</translate>
                                        </a>
                                    </div>


                                </div>
                            </div>
                        </div>
<!--                        here i need render pdf invoice component and hide html invoice or redirect to pdf invoice-->
<!--                        <div v-html="invoiceHtml" v-if="!isLoading">-->

<!--                        </div>-->

                    </div>
                </div>
            </div>

        </div>
    </div>



</template>

<script setup>
import { reactive,onMounted,ref } from 'vue';
import {AppLoader} from "@appsbd/vue3-appsbd-libs";
import {useRouter,useRoute} from "vue-router";
const router=useRouter();
import Header from "@/modules/ClientRegistration/Header.vue";
import {useClientStore} from "@/modules/ClientRegistration/ClientStore.js";
import appHelper from "@/libs/AppHelper.js";
import SmallLoader from "@/components/SmallLoader.vue";
const clientStore=useClientStore();
const isShowLoader=ref(false);
const isLoading=ref(false);
const props=defineProps({
    order:{
        type:Object,default:{},
        showHeader: { type: Boolean, default: true }
    }
});
const order = reactive({});




function goHome() {
    window.location.href = '/';
}

function contactSupport() {
    window.location.href = 'mailto:support@example.com';
}
const getInvoiceDetails= async() => {
    const route=useRoute();
    isShowLoader.value=true;
    try {
        const res=await clientStore.getInvoiceDetails({invoice_number:route.params.id});
        if(res.status)
        {
            Object.assign(order,res.data);
            console.log(order);
        }

    }catch (e) {
        console.log(e);
    }
    isShowLoader.value=false;

}
onMounted(()=>{
    // if(history.state?.order){
    //     order.value = history.state.order;
    //     console.log(order.value);
    // } else {
    //     console.log(history.state);
    // }
getInvoiceDetails();

})
const  downloadInvoice=async()=>{
    isLoading.value=true;
    try {
        const res=await clientStore.clientInvoiceByNumber({invoice_id:order.invoice_number});
        if(res.status)
        {
            router.push({
                name: "clientInvoice",
                params: { id: order.invoice_number },
                state: { html: res.data }
            });
        }

    }catch (e) {
        console.log(e);
    }
    isLoading.value=false;
}

</script>

<style scoped lang="scss">
.reg-content{
    overflow: auto;
    height: calc(100dvh - 80px);
}
@media (max-width: 768px) {
    .reg-content {
        height: calc(100dvh - 180px);
    }

}
.success-page {
    padding: 20px;
  //  background: linear-gradient(180deg, #f6fbff 0%, #ffffff 50%, #f8fff7 100%);

}

.success-card {
    display: flex;
    flex-direction: row;
    border-radius: 18px;
    overflow: hidden;
    max-width: 900px;
    width: 100%;
    background: var(--ab-card-bg);
}

@media (max-width: 768px) {
    .success-page {

    }
    .success-card {


    }
}

.visual-wrap {
    flex: 1;
    background: var(--ab-visual-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 36px;
}

.content-wrap {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* Illustration styles */
.illustration {
    position: relative;
    width: 280px;
    height: 280px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.circle-outer {
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: var(--ab-circle-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 18px 40px rgba(30,60,120,0.08);
    animation: float 4s ease-in-out infinite;
}
.check-svg {
    width: 120px;
    height: 120px;
    stroke: transparent;
    fill: transparent;
}
.check-svg circle {
    stroke: #d9e6ff;
    stroke-width: 6;
}
.check-svg path {
    stroke: #2f80ed;
    stroke-width: 7;
    stroke-dasharray: 160;
    stroke-dashoffset: 160;
    animation: draw 0.9s ease 0.2s forwards, pop 0.7s ease 1s forwards;
}

.confetti {
    position: absolute;
    inset: 0;
    pointer-events: none;
}
.c {
    position: absolute;
    width: 10px;
    height: 10px;
    border-radius: 2px;
    opacity: 0;
    transform: translateY(0) rotate(0);
}
.c1 { background: #ffd166; left: 20%; top: 18%; animation: conf 1.1s 0.1s ease forwards; }
.c2 { background: #06d6a0; left: 78%; top: 36%; animation: conf 1.3s 0.15s ease forwards; }
.c3 { background: #ff6b6b; left: 60%; top: 12%; animation: conf 1.05s 0.05s ease forwards; }
.c4 { background: #4d96ff; left: 42%; top: 6%; animation: conf 1.25s 0.12s ease forwards; }
.c5 { background: #c084fc; left: 30%; top: 46%; animation: conf 1.15s 0.08s ease forwards; }

/* Mobile illustration override */
.mobile-illustration .illustration {
    width: 200px;
    height: 200px;
}
@media (max-width: 767.98px) {
    .detail-label,
    .detail-value {
        font-size: 12px;
    }

    h3 {
        font-size: 1.25rem;
    }

    h6 {
        font-size: 0.9rem;
    }
    .circle-outer {
        width: 170px !important;
        height: 170px !important;
    }
    .check-svg {
        width: 90px !important;
        height: 90px !important;
    }
}

/* Status badge */
.status-badge {
    background: rgba(45, 212, 191, 0.12);
    color: #0f766e;
    padding: 6px 12px;
    border-radius: 999px;
    font-weight: 600;
    font-size: 13px;
}

/* Details section */
.details-section {
    background: var(--ab-card-bg);
    border-radius: 12px;
}
.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.detail-label {
    color: var(--ab-body-color);
    font-size: 14px;
}
.detail-value {
    color: var(--ab-body-color);
    font-weight: 600;
}


/* Animations */
@keyframes draw {
    to {
        stroke-dashoffset: 0;
    }
}
@keyframes pop {
    0% { transform: scale(0.96); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}
@keyframes conf {
    0% { transform: translateY(-10px) rotate(0) scale(0.6); opacity: 0; }
    30% { opacity: 1; }
    100% { transform: translateY(80px) rotate(360deg) scale(1); opacity: 0; }
}
</style>
