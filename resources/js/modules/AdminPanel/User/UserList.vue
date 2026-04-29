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
                            <button class="btn btn-sm btn-primary" @click="refreshGrid">
                                <i class="apb apb-refresh-ccw-alt"> </i>
                                <span class="ms-2" v-translate>gbl.reload</span>
                            </button>
                            <button v-if="$CheckACL('np.user-add')" class="btn btn-sm btn-primary" @click="openModal">
                                <i class="apb apb-circle-plus"> </i>
                                <span class="ms-2" v-translate>gbl.add.new</span>
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
                    action-width="100px"
                    :columns="data_column"
                    :show-loader="isShowLoader"
                    :show-header="false"
                    :grid-data="gridData"
                    :show-action-column="true"
                    :is-show-row-index-column="true"
                    @load-data="eliteGridLoadData"
                >
                    <template v-slot:slot-loader>

                        <APBDGridLoader :msg="appsbdUtls.translateGettext('gbl.loading',{name:'user'})" />
                    </template>
                    <template v-slot:slot-no-record>
                        {{
                            appsbdUtls.translateGettext('No %{type} found', { type: 'User' })
                        }}
                    </template>
                    <template v-slot:slotstatus="{rowitem}">
                          <span :class="rowitem.status=='A'?'text-success':'text-danger'" style="cursor: pointer;" @click="changeStatus(rowitem)">
                            {{ appsbdUtls.translateGettext(getStatus(rowitem.status)) }}
                          </span>
                    </template>
                    <template v-slot:actionProperty="slotProps">
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="$CheckACL('np.user-update')" class="btn btn-primary btn-sm  d-flex align-items-center gap-2" @click="showModal(slotProps.rowitem.id)">
                                <i class="apb apb-edit-01"></i>
                                <translate>gbl.edit.now</translate>
                            </button>
                            <button v-if="$CheckACL('np.user-delete')" class="btn  btn-sm btn-danger  d-flex align-items-center gap-2" @click="deleteUser(slotProps.rowitem.id)">
                                <i class="apb apb-gear"></i>
                                <translate>gbl.delete</translate>
                            </button>
                        </div>
                    </template>
                </elite-grid>
            </div>
        </div>
    </div>
    <AddUserModal v-if="isShowModal" :user_id="userId" @close="closeModal" @reload="refreshGrid" />
</template>
<script setup>
import { ref, reactive,  onMounted,getCurrentInstance } from 'vue'
const { proxy } = getCurrentInstance();


import EliteGrid from '@appsbd/vue3-elite-grid'
import { EliteColumnModel } from '@appsbd/vue3-elite-grid'
import APBDRequestParam from '@/libs/APBDRequestParam'
import { ApbdFilterPanel } from '@appsbd/vue3-appsbd-libs'

import {useLoginStore} from "@/modules/AdminPanel/User/loginStore.js";
import{useUserStore} from "@/modules/AdminPanel/User/UserStore.js";
import appsbdUtls from '@/libs/AppsbdUtls.js'

import APBDGridLoader from '@/components/APBDGridLoader.vue'
import AddUserModal from '@/modules/AdminPanel/User/AddUserModal.vue'
import AppsbdUtls from '@/libs/AppsbdUtls.js'


const userStore=useUserStore()
const isShowLoader = ref(false)
const isShowModal = ref(false)

const userId = ref(null)

const gridData = reactive({
    page: 1, total: 1, records: 0,
    limit: 20, rowdata: []
})
const searchProps = ref([])
const sortProps = ref(null)
const data_column = [
    EliteColumnModel.getColumn({ name: 'name', title: 'name', width: '150px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'email', title: 'gbl.email', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'username', title: 'gbl.username', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'role_name', title: 'gbl.role', width: '100px', is_sortable: false }),
    EliteColumnModel.getColumn({ name: 'status', title: 'gbl.status', width: '100px', is_sortable: true }),

]

const filterProps = [
    {
        id: 1,
        name: appsbdUtls.translateGettext('Name'),
        propName: 'name',
        type: 't',
        options: [],
        operators: 'like',
        value: '',
    },
    {
        id: 2,
        name: appsbdUtls.translateGettext('Email'),
        propName: 'email',
        type: 't',
        options: [],
        operators: 'eq',
        value: ''
    },
    {
        id: 3,
        name: appsbdUtls.translateGettext('Username'),
        propName: 'username',
        type: 't',
        options: [],
        operators: 'eq',
        value: ''
    },
    {
        id: 4,
        name: appsbdUtls.translateGettext('Status'),
        propName: 'status',
        type: 'dd',
        optionLabel: "name",
        optionValueProp: "val",
        options: [
            {'name':appsbdUtls.translateGettext('Active'),'val':'A'},
            {'name':appsbdUtls.translateGettext('Inactive'),'val':'I'}],
        operators: 'eq',
        value: '',
    },
];

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
        const response = await userStore.getUsers(param)
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
// need to discuss
async function sendEmail(id) {
    const confirmOptions = {
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#2563EB',
        confirmButtonText: AppsbdUtls.translateGettext('gbl.yes'),
        cancelButtonText: AppsbdUtls.translateGettext('gbl.no'),
        showLoaderOnConfirm: true
    }
    AppsbdUtls.ShowConfirmRequest(AppsbdUtls.translateGettext('gbl.send.email'),
        async () => {
            const resp =  await userStore.sendResetMail({ id:id })
            return resp
        },
        confirmOptions
    )

}
// async function sendResetMail(id)
// {
//
//     isShowLoader.value = true
//     try {
//         const response = await userStore.sendResetMail({ id:id })
//     }catch (e) {
//         console.log(e);
//     }
//     isShowLoader.value=false
//
// }
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

function getStatus(sta) {
    return (sta == 'A' ? 'gbl.active' : 'gbl.inactive')
}

function showModal(id) {
    if (id != null) {
        userId.value = id
    }

    isShowModal.value = true
}
function closeModal() {
    userId.value = null
    isShowModal.value = false
}

const changeStatus = async (obj) => {
    if (!obj || !proxy.$CheckACL('np.user-update')) return

    const confirmMsg = obj.status === 'A'
        ? AppsbdUtls.translateGettext('gbl.user.inactive',{name:'gbl.user'})
        : AppsbdUtls.translateGettext('gbl.user.active',{name:'gbl.user'})

    appsbdUtls.ShowConfirmRequest(
        confirmMsg,
        async () => {
            try {
                const response = await userStore.updateUser({
                    id: obj.id,
                    status: obj.status === 'A' ? 'I' : 'A'
                })

                if (response.status) {
                    loadGridData()
                }

                return response
            } catch (error) {
                console.error(error)
            }
        },
        {
            showCancelButton: true,
            confirmButtonColor: '#2563EB',
            cancelButtonColor: '#dc3545',
            confirmButtonText: appsbdUtls.translateGettext('Change'),
            cancelButtonText: appsbdUtls.translateGettext('No'),
            showLoaderOnConfirm: true
        }
    )
}


function deleteUser(id) {
    const confirmOptions = {
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#2563EB',
        confirmButtonText: appsbdUtls.translateGettext('gbl.delete'),
        cancelButtonText: appsbdUtls.translateGettext('gbl.no'),
        showLoaderOnConfirm: true
    }
    appsbdUtls.ShowConfirmRequest(AppsbdUtls.translateGettext('gbl.delete.project',{name:'gbl.user'}),
        async () => {
            const resp = await userStore.deleteUser({ user_id: id })
            if (resp.status) {
                loadGridData()
            }
            return resp
        },
        confirmOptions
    )
}

onMounted(() => loadGridData())

</script>
<style scoped lang="scss">
.apbd-hover{
    font-size: 16px;
    &:hover{
        color:var(--ab-main-color);
    }

}
.badge {
    cursor: pointer;

    &.bg-success {
        &:hover {
            background: var(--ab-main-color, #defedf) !important;
        }
    }

}
</style>
