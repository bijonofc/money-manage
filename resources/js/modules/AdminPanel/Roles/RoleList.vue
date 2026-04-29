<template>
  <div>
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-sm-8">
            <apbd-filter-panel :is-single="true" @searchFilter="this.searchData" @reset="this.clearSearch" />
          </div>
          <div class="col-sm-4 text-end">
          <button v-if="$CheckACL('np.role-add')" class="btn btn-sm btn-theme" @click="showModal()" v-translate>add.role</button>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-3">
      <div class="elite-grid-container">
        <elite-grid
            :is-rounded="true"
            :is-group-separate-head="true"
            action-width="200px"
            :columns="data_column"
            :show-loader="isDataLoader"
            :show-header="false"
            :show-action-column="$CheckACL('np.role-update')|| $CheckACL('np.role-delete')"
            :grid-data="gridData"
            :is-show-row-index-column="true"
            @loadData="eliteGridLoadData"
        >
          <template v-slot:slotname="slotProps">
            {{ slotProps.rowitem.name }}
            <span class="text-success" v-if="slotProps.rowitem.is_editable=='N'">
            ({{ appsbdUtls.translateGettext("Built-in") }})
          </span>
          </template>
          <template v-slot:slotstatus="slotProps">
            <span :class="slotProps.rowitem.status =='A'?'text-success':'text-warning'" @click="changeStatus(slotProps.rowitem)"  role="button">
              {{ slotProps.rowitem.status =='A'? appsbdUtls.translateGettext('gbl.active'):appsbdUtls.translateGettext('gbl.inactive')}}
            </span>
          </template>
          <template v-slot:slot-loader >
              <APBDGridLoader msg="gbl.loading" :msg-params="{name:'roles'}"/>
          </template>
          <template v-slot:actionProperty="slotProps">
            <div v-if="slotProps.rowitem.slug!='super-admin'">
              <a v-if="$CheckACL('np.role-update')" class="btn btn-sm btn-primary me-2" @click="showModal(slotProps.rowitem.id)">
                <i class="apb apb-edit-01"></i> <span v-translate>gbl.edit.now</span>
              </a>
              <a v-if="$CheckACL('np.role-delete')" class="btn  btn-sm btn-danger" @click="deleteRoleModal(slotProps.rowitem)">
                <i class="apb apb-trash-3 "></i> <span v-translate>gbl.delete</span>
              </a>
            </div>
            <div v-else>-</div>
          </template>
        </elite-grid>
      </div>
    </div>
  </div>
  <modal v-show="isShowModal" :modal-msg="msg" modal-size="modal-md" ref="role_modal" @onSubmit="addRole($event)"
         @loading-status="loaderStatusChange" @close="closeModal">
    <template v-slot:header>
      <span> {{ add_props.id ? this.$gettext('gbl.edit.now') : this.$gettext('gbl.add.now') }}</span>

    </template>
    <template v-slot:body>
       <role-add-form :form-props="add_props" />
    </template>
    <template v-slot:footer>
      <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" @click="closeModal" v-translate>
       gbl.cancel
      </button>
      <button type="submit" class="btn btn-sm btn-primary" data-dismiss="modal">
        {{ add_props.id ? this.$gettext('gbl.update') : this.$gettext('gbl.add.now') }}
      </button>
    </template>
  </modal>
  <modal v-show="isShowDeleteModal" :modal-msg="msg" modal-size="modal-md" ref="delete_role_modal" @onSubmit="deleteRole($event)"
          @close="closeDeleteModal">
    <template v-slot:header>
      <span> {{ appsbdUtls.translateGettext('gbl.delete') }}</span>
    </template>
    <template v-slot:body>
      <role-delete-form ref="role-delete-form" :form-props="delete_props"  />
      <div class="form-row">
        <label >
          <div class="form-check form-switch form-switch-sm mt-0">
            <input v-model="delete_agree" class="form-check-input"  type="checkbox" id="status" name="status">
          </div>
          <span >
            <span class="b-agree-ctrn" v-html="getAgreedRole"></span>
          </span>
        </label>
      </div>
    </template>
    <template v-slot:footer="{close}">
      <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" @click="close" v-translate>
        gbl.cancel
      </button>
      <button :disabled="getDeletePermission" type="submit" class="btn btn-sm btn-theme" data-dismiss="modal">
        {{ appsbdUtls.translateGettext('gbl.delete') }}
      </button>
    </template>
  </modal>

</template>

<script>

import EliteGrid from '@appsbd/vue3-elite-grid';
import {EliteColumnModel} from '@appsbd/vue3-elite-grid';
import {mapStores} from 'pinia'
import {useRoleStore} from "./role";
import APBDGridLoader from "@/components/APBDGridLoader";
import RoleAddForm from "@/modules/AdminPanel/roles/RoleAddForm";
import RoleDeleteForm from "@/modules/AdminPanel/roles/RoleDeleteForm";
import APBDRequestParam from "@/libs/APBDRequestParam";
import {ApbdFilterPanel,Modal,ResponseMsg} from '@appsbd/vue3-appsbd-libs'
import appsbdUtls from "../../../libs/AppsbdUtls.js";
import app from "vue3-perfect-scrollbar/example/App.vue";

export default {
  name: "RoleList",
  components: {
    ApbdFilterPanel,
    RoleDeleteForm,
    RoleAddForm,
    ResponseMsg,
    APBDGridLoader,
    Modal,
    EliteGrid
  },
  data() {
    return {
      module_id: "POS_Role",
      isShowModal: false,
      isShowDeleteModal: false,
      isShowCounterModal: false,
      isShowLoader: false,
      isDataLoader: false,
      msg: {},
      selectedOutlet: {
        id: '',
        name: '',
        contact_no: '',
        address: '',
        country: ''
      },
      gridData: {
        page: 1,
        total: 1,
        records: 0,
        limit: 20,
        rowdata: []
      },
      add_props: {status:'I'},
      delete_props: {},
      delete_role_item:{},
      delete_agree:false,
      currentProps: {},
      searchProps:[],
      sortProps:null,
      data_column: [
        EliteColumnModel.getColumn({name: "title", title: "gbl.title", width: '200px', is_sortable: true}),
        EliteColumnModel.getColumn({name: "status", title: "gbl.status", width: '200px', is_sortable: false,align:'center',title_align:'center'}),
      ],
    }
  },
  mounted() {
    if (!this.roleStore.firstLoaded || !(this.roleStore.gridData && this.roleStore.gridData.records)) {
      this.loadGridData();
    } else {
      try {
        if (this.roleStore.gridData.records) {
          this.gridData.limit = this.roleStore.gridData.limit;
          this.gridData.records = this.roleStore.gridData.records;
          this.gridData.total = this.roleStore.gridData.total;
          this.gridData.rowdata = this.roleStore.gridData.rowdata;
        } else {
          this.loadGridData();
        }
      } catch (e) {
        this.loadGridData();
      }
    }
  },
  computed: {
      getDeletePermission() {
          if (!this.delete_agree || this.delete_props.slug === '' || this.delete_props.slug === undefined) {
              return true;
          }
          return false;
      },
      app() {
          return app
      },
      appsbdUtls() {
          return appsbdUtls
      },
    getAgreedRole(){
      if(this.delete_role_item?.title) {
        return this.$translate.$gettext('I agree to delete the %{rolename} and move all users of %{rolename} to the selected role').replaceAll('%{rolename}','<b class="text-success">'+this.delete_role_item?.title+'</b>');
      }else{
        return 'I agree to delete the ----- and move all users of --- to the selected role';
      }
    },
    ...mapStores(useRoleStore)
  },
  methods: {
    searchData(data)
    {
      this.searchProps=data;
      this.gridData.page = 1;
      this.loadGridData();
    },
    clearSearch(){
      this.searchProps=[];
      this.loadGridData();
    },

    clearForm(){
      this.add_props={};
      this.currentProps={};
      this.$refs.role_modal.clearForm();
    },
    deleteRole_old(item) {
      var thisObj = this;
      this.$appsbdUtls.ShowConfirmRequest(this.$translateGettext('Are you sure to delete this outlet: %{role}?', {role: item.name}), async function () {
        let response = await thisObj.roleStore.deleteRole(item.id);
        if (response.status) {
          thisObj.loadGridData();
        }
        return response;

      });
    },
    changeStatus(obj){
      if (this.$CheckACL('np.role-update')){
        let thisObj = this;
        this.$appsbdUtls.ShowConfirmRequest(obj.status=='A'?this.$translateGettext('gbl.user.inactive',{name:'gbl.role'}):this.$translateGettext('gbl.user.inactive',{name:'gbl.role'}), async function () {
          let response= await thisObj.roleStore.updateRole({id:obj.id,status:obj.status=='A'?'I':'A'});
          if (response.status)
          {
            thisObj.loadGridData();
          }
          return response;
        }, {showCancelButton: true,
          confirmButtonColor: '#2563EB',
          cancelButtonColor: '#dc3545',
          confirmButtonText: this.$gettext("Change"),
          cancelButtonText: this.$gettext("No"),
          showLoaderOnConfirm: true,});
      }
    },
    closeModal() {
      this.isShowModal = false;
      this.msg = {};
      this.clearForm();
      this.delete_role_item={};
    },
    closeDeleteModal() {
      this.isShowDeleteModal = false;
      this.msg = {};
      this.$refs.delete_role_modal.clearForm();
      this.clearForm();
    },
    eliteGridLoadData(gparam) {
      this.gridData.limit = gparam.limit;
      this.gridData.page = gparam.page;
      if (gparam.sort_prop) {
        this.sortProps={prop:gparam.sort_prop,ord:gparam.sort_ord};
      }else{
        this.sortProps=null;
      }
      this.loadGridData();
    },
    async loadGridData() {
      this.isDataLoader = true;
      try {
        const param = new APBDRequestParam();
        param.limit=this.gridData.limit;
        param.page=this.gridData.page;

        if(this.searchProps.length>0){
          for (let i=0;i<this.searchProps.length;i++)
          {
            param.AddSrcItem(this.searchProps[i].propName,this.searchProps[i].value,this.searchProps[i].operators);
          }
        }
        if(this.sortProps){
          param.AddSortItem(this.sortProps.prop,this.sortProps.ord);
        }
        let response = await this.roleStore.getData(param);
        if (response) {
          this.gridData.page = response.page;
          this.gridData.limit = response.limit;
          this.gridData.records = response.records;
          this.gridData.total = response.total;
          this.gridData.rowdata = response.rowdata;
        }
      } catch (e) {

      }
      this.isDataLoader = false;
    },

    async deleteRole({resetForm}) {
      this.msg = {};
      this.$refs.delete_role_modal.showLoader(true, this.$gettext('Delete Role'));
      let response = await this.roleStore.deleteRole(this.delete_props);
      this.$refs.delete_role_modal.showLoader(false, this.$gettext('Delete Role'));
      this.msg = response.msg;
      if (response.status) {
        this.$refs.delete_role_modal.setMessageOnly(true);
        await this.roleStore.setFirstLoad(false);
        this.loadGridData();
      }
    },
    async addRole({resetForm}) {
      this.msg={};
      if (this.add_props.id) {
        let changedFormData = this.$appsbdUtls.changedFormData(this.add_props,this.currentProps);
        if (Object.keys(changedFormData).length === 0) {
          let resp= {error:['No changer found for update']};
          this.msg=resp;
          return;
        } else {
          changedFormData['id']=this.add_props.id;
          this.$refs.role_modal.showLoader(true, this.$gettext('update.role'));
          let response = await this.roleStore.updateRole(changedFormData);
          this.$refs.role_modal.showLoader(false);
          this.msg = response.msg;
          if (response.status) {
            await this.roleStore.setFirstLoad(false);
            this.$refs.role_modal.setMessageOnly(true);
            this.loadGridData();
          }
        }
      } else {
        this.$refs.role_modal.showLoader(true, this.$gettext('adding.role'));
        let response = await this.roleStore.addRole(this.add_props);
        this.$refs.role_modal.showLoader(false);
        this.msg = response.msg;
        if (response.status) {
          this.clearForm();
          this.$refs.role_modal.setMessageOnly(true);
          await this.roleStore.setFirstLoad(false);
          this.loadGridData();
        }
      }
    },
    async deleteRoleModal(role) {
      this.msg = {};
      this.$refs['role-delete-form'].SetRole(role);
      this.delete_role_item=role;
      this.delete_agree=false;
      this.isShowDeleteModal = true;


    },
    async   showModal(id) {
      this.msg = {};
      if (id) {
        this.isShowModal = true;
        this.$refs.role_modal.showLoader(true, this.$gettext('load.role'));
        let response = await this.roleStore.getRoleDetails({id: id});
        this.$refs.role_modal.showLoader(false);
        this.msg = response.msg;
        if (response.status) {
          this.add_props = {...this.add_props,...response.data};
          this.currentProps = {...response.data};
        }
      } else {
        this.isShowModal = true;
      }
    },

    loaderStatusChange(param) {
      this.isShowLoader = param;
    },
    loaderDeleteModalStatusChange(param) {
      this.isShowDeleteModal = param;
    },
  }
}
</script>

<style scoped lang="scss">

</style>
