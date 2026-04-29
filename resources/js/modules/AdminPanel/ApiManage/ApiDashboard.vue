<template>
    <div class="">
        <app-loader :msg="appsbdUtls.translateGettext('Loading type',{ type: 'API Data' })" v-if="isShowLoader" />
        <div v-else>
            <section-title icon="apb-users-01" title="Api Notification information" />
            <div class="row g-4 mb-4">
                <StatBox label="Sms Balance" :value="d?.sms_balance" icon="apb-user-plus" color="blue"/>
            </div>
            <div class="card  p-3 mb-4">
                <h5 class="mb-3" v-translate>Message log summery</h5>
                <div class="table-responsive">
                    <table class="table  align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th v-translate>gbl.channel</th>
                            <th v-translate>gbl.success</th>
                            <th v-translate>gbl.failed</th>
                            <th v-translate>gbl.total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(log, i) in d?.logs" :key="i">
                            <td>{{ appHelper.toBanglaDigits(i+1) }}</td>
                            <td>{{ appsbdUtls.translateGettext(log.channel) }}</td>
                            <td>{{ appHelper.toBanglaDigits(log.success) }}</td>
                            <td>{{ appHelper.toBanglaDigits(log.failed) }}</td>
                            <td>{{ appHelper.toBanglaDigits(log.total) }}</td>


                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, computed, onMounted} from "vue";
import { AppLoader } from "@appsbd/vue3-appsbd-libs";

// Components
import StatBox from "@/components/StatBox.vue";
import SectionTitle from "@/components/SectionTitle.vue";
import {useApiStore} from "@/modules/AdminPanel/ApiManage/ApiStore.js";
import appsbdUtls from "../../../libs/AppsbdUtls.js";
import appHelper from "../../../libs/AppHelper.js";

const apiStore = useApiStore();
const isShowLoader = ref(false);

const d = ref({});

async function load() {
    try {
        isShowLoader.value = true;
        const res=await apiStore.getInitialData();
        d.value = res.data;
        console.log(res);
    }catch (e) {
       console.log(e);
    }
    isShowLoader.value = false;
}

onMounted(load);
</script>

<style scoped lang="scss">

</style>
