
    <template>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-sm-8">
                                <apbd-filter-panel :filter-options="filterProps" @searchFilter="searchData" @reset="clearSearch" />
                            </div>
                            <div class="col-sm-4  d-flex align-items-center justify-content-end gap-2">
                                <button v-if="$CheckACL('np.transaction-list')" class="btn btn-sm btn-primary" @click="refreshGrid">
                                    <i class="apb apb-refresh-ccw-alt"> </i>
                                    <span class="ms-2" v-translate>gbl.reload</span>
                                </button>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="col-sm-12 mt-3">
                    <elite-grid
                        :is-rounded="true"
                        :is-group-separate-head="true"
                        action-width="150px"
                        :columns="data_column"
                        :show-loader="isShowLoader"
                        :show-header="false"
                        :grid-data="gridData"
                        :show-action-column="true"
                        :is-show-row-index-column="true"
                        @load-data="eliteGridLoadData"
                    >
                        <template v-slot:slot-loader>
                            <APBDGridLoader msg="gbl.loading" :msg-params="{name:'transaction'}"/>
                        </template>
                        <template v-slot:slot-no-record>
                            {{
                                appsbdUtls.translateGettext('No %{type} found', { type: 'transaction' })
                            }}
                        </template>
                        <template v-slot:slotstatus="{rowitem}">
                          <span :class="getStatusClass(rowitem.status)">
                            {{ getStatus(rowitem.status) }}
                          </span>
                        </template>
                        <template v-slot:slotamount="{rowitem}">
                          <span class="text-success">
                            {{ appHelper.toLocalizedDigits(rowitem.amount,'bn') }}
                              <translate>gbl.tk</translate>
                          </span>
                        </template>

                        <template v-slot:slotpaid_at="{ rowitem }">
                            <span class="text-success">
                             {{ appHelper.formatDate(rowitem.paid_at) }}
                            </span>
                        </template>
                        <template v-slot:slotis_verified="{ rowitem }">
                            <span :class="rowitem.is_verified=='Y'?'text-success':'text-danger'">
                            {{ getExt(rowitem.is_verified)}}
                            </span>
                        </template>


                        <template v-slot:actionProperty="slotProps">
                            <div class="d-flex gap-2 justify-content-center">
                                <button v-if="$CheckACL('np.transaction-detail')" class="btn btn-primary btn-sm  d-flex align-items-center" @click="showModal(slotProps.rowitem.id)">
                                    <i class="apb apb-edit-01 me-2"></i>
                                    <translate>gbl.details</translate>
                                </button>

                                <button v-if="$CheckACL('np.transaction-log')" class="btn btn-primary btn-sm  d-flex align-items-center" @click="showLogModal(slotProps.rowitem.id)">
                                    <i class="apb apb-edit-01 me-2"></i>
                                    <translate>gbl.log</translate>
                                </button>
                            </div>
                        </template>
                    </elite-grid>
                </div>
            </div>
        </div>
        <transaction-detail :id="trx_id" v-if="isShowModal" @close="closeModal" @reload="refreshGrid"  />
        <transaction-log :id="trx_id" v-if="isShowLogModal" @close="closeModal" @reload="refreshGrid"  />
    </template>
    <script setup>
        import { ref, reactive, computed, onMounted } from 'vue'
       import {useTransactionStore} from "@/modules/AdminPanel/Transaction/TransactionStore.js";
        import appsbdUtls from "@/libs/AppsbdUtls.js";
        import appHelper from "@/libs/AppHelper.js";
        import EliteGrid from '@appsbd/vue3-elite-grid'
        import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
        import APBDRequestParam from '@/libs/APBDRequestParam'
        import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'
        import TransactionDetail from "@/modules/AdminPanel/Transaction/TransactionDetail.vue";
        import TransactionLog from "@/modules/AdminPanel/Transaction/TransactionLog.vue";
        import APBDGridLoader from "@/components/APBDGridLoader.vue";
        const gridData = reactive({
            page: 1, total: 1, records: 0,
            limit: 20, rowdata: []
        })
        const searchProps = ref([])
        const sortProps = ref(null)
        const isShowLoader=ref(false)
        const isShowModal=ref(false)
        const isShowLogModal=ref(false)
        const trx_id=ref(null)
        const transactionStore=useTransactionStore();
        const data_column = [
            EliteColumnModel.getColumn({ name: 'invoice_uuid', title: 'gbl.invoice.number', width: '150px', is_sortable: false }),
            EliteColumnModel.getColumn({ name: 'c_name', title: 'gbl.customer.name', width: '100px', is_sortable: false, }),
            EliteColumnModel.getColumn({ name: 'amount', title: 'gbl.total.price', width: '50px', is_sortable: false, }),
            EliteColumnModel.getColumn({ name: 'status', title: 'gbl.status', width: '50px', is_sortable: false, }),
            EliteColumnModel.getColumn({ name: 'is_verified', title: 'gbl.is.verified', width: '50px',align:'center',title_align:'center', is_sortable: false, }),
            EliteColumnModel.getColumn({ name: 'paid_at', title: 'gbl.paid.at', width: '80px', is_sortable: false, }),
            EliteColumnModel.getColumn({ name: 'payment_method', title: 'gbl.payment.method', width: '120px', is_sortable: false, }),

        ];

        const filterProps = [

            { id: 1,
                name: 'Invoice',
                propName: 'invoice_uuid',
                options: [],
                type: 't',
                operators: 'eq',
                value: '',
            }
        ];


        onMounted(() => loadGridData())
        async function loadGridData() {
            isShowLoader.value = true
            const param = new APBDRequestParam()
            param.limit = gridData.limit
            param.page = gridData.page
            for (const item of searchProps.value) {
                param.AddSrcItem(item.propName, item.value, item.operators)
            }
            if (sortProps.value) {
                param.AddSortItem(sortProps.value.prop, sortProps.value.ord)
            }
            try {
                const response = await transactionStore.getData(param)
                if (response) {
                    gridData.page = response.page
                    gridData.limit = response.limit
                    gridData.records = response.records
                    gridData.total = response.total
                    gridData.rowdata = response.rowdata
                }
            } catch (e) {
                console.error(e)
            }
            isShowLoader.value = false
        }

        function searchData(data) {
            searchProps.value = data
            gridData.page = 1
            loadGridData()
        }
        function clearSearch() {
            searchProps.value = []
            loadGridData()
        }

        function refreshGrid() {
            loadGridData()
        }

        function eliteGridLoadData(gparam) {
            gridData.limit = gparam.limit
            gridData.page = gparam.page
            sortProps.value = gparam.sort_prop ? { prop: gparam.sort_prop, ord: gparam.sort_ord } : null
            loadGridData()
        }

        function openModal() {
            isShowModal.value = true
        }
        function showModal(id) {
            if (id != null) {
                trx_id.value = id
            }
            isShowModal.value = true
        }
        function showLogModal(id) {

            if (id != null) {
                trx_id.value = id
            }
            isShowLogModal.value = true
        }
        function closeModal() {
            trx_id.value = null
            isShowModal.value = false
            isShowLogModal.value = false
        }

        function getStatus(status) {
            return status === "S" ? "Success" : status === "P"?"Pending":status === "F"?"Failed":"Cancelled";
        }
        function getStatusClass(status) {
            return status === "S" ? 'text-success': status === "P"? 'text-warning':'text-danger';
        }
        function getExt(type) {
            return type === "Y" ? appsbdUtls.translateGettext("gbl.yes") : appsbdUtls.translateGettext("gbl.no");
        }
    </script>
    <style scoped lang="scss">

</style>
