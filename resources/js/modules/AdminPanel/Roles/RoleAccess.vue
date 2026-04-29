<template>
  <div>
    <div class="card mb-3">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-sm-8">
            <apbd-filter-panel :is-single="true" @searchFilter="this.searchData" @reset="this.clearSearch" />
          </div>
          <div class="col-sm-4 text-end">
            <button type="button" @click="isShowModal = !isShowModal" class="btn btn-sm btn-theme me-2"> <i class="apb vps-des-repeat me-2"></i> <translate>reset.role</translate></button>
            <button type="button" @click="showModal" class="btn btn-sm btn-theme"> <i class="apb vps-copy1 me-2"></i> <translate>copy.role.permission</translate></button>
          </div>
        </div>
      </div>
    </div>
    <div class="mb-3 ">
      <div class="elite-grid-container">
        <elite-grid
            :is-rounded="true"
            :is-group-separate-head="true"
            action-width="200px"
            :columns="data_column"
            :show-loader="isDataLoader"
            :show-header="false"
            :hide-pagination="true"
            :show-action-column="false"
            :grid-data="gridData"
            :is-show-row-index-column="true"

            @loadData="eliteGridLoadData"
        >
          <template v-slot:slot-loader >
            <APBDGridLoader msg="load.access" />
          </template>
          <template v-slot:slottitle="titleslotProps">
            {{ titleslotProps.rowitem.title }} <i v-if="titleslotProps.rowitem.tooltip_note!=''" v-tooltip="this.$translateGettext(titleslotProps.rowitem.tooltip_note)" class="apb vps-help-circle apbd-pointer"></i>
          </template>

          <template v-slot:[`slot${role.slug}`]="slotProps" v-for="role in roleStore.getRoles">
            <span :class="(role.is_super=='Y'?'text-theme':'' || slotProps.rowitem.role_access.includes(role.id)?' text-theme ':' text-danger ')+ (role.editable && $CheckACL('np.change-access')?' apbd-pointer':' apbd-text-bold')"  @click="changePermission(slotProps.rowitem,role)">
              <i class="apb " :class=" role.is_super=='Y'?'apb-check':'' ||  slotProps.rowitem.role_access.includes(role.id)?'apb-check':'apb-x-close small'"></i>
            </span>
          </template>
        </elite-grid>
      </div>
    </div>
  </div>
  <modal v-show="isShowModal" modal-size="modal-md"  :modal-msg="msg" ref="reset_modal" @onSubmit="resetRole($event)"
         @loading-status="loaderStatusChange" @close="closeModal">
    <template v-slot:header>
      <span> {{ this.$gettext('reset.role') }}</span>
    </template>
    <template v-slot:body>
      <div class="row">
        <div class="col-sm">
          <div class=" mb-2">
            <label for="role" v-translate>select.reset</label>
           <multiselect id="role" v-model="add_props.selected_role"  label="title" valueProp="id"
                         :placeholder="this.$gettext('search.role')" :searchable="true" :options="roleList"
            ></multiselect>
            <small v-if="add_props?.selected_role" class="help-text text-warning small-note text-italic" v-translate>
              warning.role
            </small>
          </div>
        </div>
      </div>
    </template>
    <template v-slot:footer>
      <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" @click="closeModal" v-translate>
        gbl.cancel
      </button>
      <button type="submit" :disabled="add_props?.selected_role==null" class="btn btn-sm btn-theme" data-dismiss="modal">
        {{ this.$gettext('Reset') }}
      </button>
    </template>
  </modal>
  <modal v-show="isShowCopyModal" modal-size="modal-md"  :modal-msg="msg" ref="copy_modal" @onSubmit="copyRolePermission($event)"
         @loading-status="loaderStatusChange" @close="closeCopyModal">
    <template v-slot:header>
      <span> {{ this.$gettext('copy.role.permission') }}</span>
    </template>
    <template v-slot:body>
      <div class="row">
        <div class="col-sm">
          <div class=" mb-2">
            <label for="role" v-translate>copy.from</label>
           <multiselect id="from" v-model="add_props.from"  label="title" valueProp="id"
                         :placeholder="this.$gettext('search.role')" :searchable="true" :options="this.roleStore.getRoles"
            ></multiselect>
          </div>
        </div>
        <div class="col-sm">
          <div class=" mb-2">
            <label for="role" v-translate>copy.to</label>
           <multiselect id="to" v-model="add_props.to"  label="title" valueProp="id"
                         :placeholder="this.$gettext('search.role')" :searchable="true" :options="roleList"
            ></multiselect>
          </div>
        </div>
      </div>
    </template>
    <template v-slot:footer>
      <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" @click="closeCopyModal" v-translate>
        gbl.cancel
      </button>
      <button type="submit" :disabled="add_props?.from==null || add_props?.to==null || add_props?.from==add_props?.to" class="btn btn-sm btn-theme" data-dismiss="modal">
        {{ this.$gettext('copy.role') }}
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
import Multiselect from '@vueform/multiselect'
import {ApbdFilterPanel,Modal,ResponseMsg} from '@appsbd/vue3-appsbd-libs'

export default {
  name: "RoleAccess",
  components: {
    ApbdFilterPanel,
    ResponseMsg,
    APBDGridLoader,
    Modal,
    Multiselect,
    EliteGrid
  },
  data() {
    return {
      module_id: "POS_Role",
      isShowModal: false,
      isShowCopyModal: false,
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
        limit: 100,
        rowdata: []
      },
      add_props: {},
      currentProps: {},

    }
  },
  async mounted() {
    if (!this.roleStore.firstLoaded || !(this.roleStore.gridData && this.roleStore.gridData.records)) {
      this.isDataLoader = true;
      let response = await this.roleStore.getData();
      this.isDataLoader = false;
    }
    if (!this.roleStore.firstAccessLoaded || !(this.roleStore.accessGridData && this.roleStore.accessGridData.records)) {
      this.loadGridData();
    } else {
      try {
        if (this.roleStore.gridData.records) {
          this.gridData.records = this.roleStore.accessGridData.records;
          this.gridData.total = this.roleStore.accessGridData.total;
          this.gridData.rowdata = this.roleStore.accessGridData.rowdata;
        } else {
          this.loadGridData();
        }
      } catch (e) {
        this.loadGridData();
      }
    }
  },
  computed: {
    ...mapStores(useRoleStore),
    changedFormData() {
      return Object.keys(this.add_props).reduce((formData, field) => {
        if (this.add_props[field] !== this.currentProps[field]) {
          formData[field] = this.add_props[field];
        }
        return formData;
      }, {});
    },
    data_column(){
      let cols=[];
      cols.push(EliteColumnModel.getColumn({name: "group_title", title: "Module", width: '200px', is_group_by: true}));
      cols.push(EliteColumnModel.getColumn({name: "title", title: "Action", width: '200px'}));
      try {
        this.roleStore.getRoles.forEach((value, index) => {
          cols.push(EliteColumnModel.getColumn({name: value.slug, title: value.title, width: '200px', title_align:'center', align:'center'}));
        });
      }catch(e){
        console.log(e.message);
      }
      return cols;
    },
    roleList(){
      try {
        return this.roleStore.getRoles.filter(role => role.is_super !='Y' )
      }catch  {
        return [];
      }
    }
  },
  methods: {
    async changePermission(item,role){
      if(!role.editable){
        return ;
      }
      if(!this.$CheckACL('np.change-access')){
          this.$appsbdUtls.ShowNotificationbyType(this.$translateGettext('gbl.permission.denied'),'e');
        return ;
      }

      let thisObj = this;
      let alertMsg="";
      if(item.role_access.includes(role.id)){
        alertMsg=this.$translateGettext('remove.access', {role: role.title});
      }else{
        alertMsg=this.$translateGettext('give.access', {role: role.title});
      }
      this.$appsbdUtls.ShowConfirmRequest(alertMsg, async function () {
        let response = await thisObj.roleStore.changePermission({res:item.res,role_slug:role.slug,role_id:role.id});
          console.log(response);
          if (response.status) {
          if (response.data.role_access=='Y')
          {
            item.role_access.push(role.id);
          }else {
            item.role_access.splice(item.role_access.indexOf(role.id),1);
          }
        }
        return response;
      },{confirmButtonText: thisObj.$translateGettext("gbl.yes"),cancelButtonText: thisObj.$translateGettext("No"),});
    },
    closeModal() {
      this.isShowModal = false
      this.msg={};
      this.add_props={};
      this.$refs.reset_modal.clearForm();
    },
    closeCopyModal() {
      this.isShowCopyModal = false
      this.msg={};
      this.add_props={};
      this.$refs.copy_modal.clearForm();
    },
    eliteGridLoadData(gparam) {
      this.gridData.limit = gparam.limit;
      this.gridData.page = gparam.page;
      this.loadGridData();

    },
    async loadGridData() {
      this.isDataLoader = true;
      try {
        let response = await this.roleStore.getAccessData();
        if (response) {
          this.gridData.records = response.records;
          this.gridData.total = response.total;
          this.gridData.rowdata = response.rowdata;
        }
      } catch (e) {

      }
      this.isDataLoader = false;
    },
    async resetRole() {
      if (this.add_props.selected_role !=null) {
        this.$refs.reset_modal.showLoader(true, "Resetting role");
        let response = await this.roleStore.resetRole(this.add_props);
        this.$refs.reset_modal.showLoader(false);
        this.msg=response.msg;
        if (response.status) {
          this.$refs.reset_modal.clearForm();
          this.add_props.selected_role=null;
          this.$refs.reset_modal.setMessageOnly(true);
          this.loadGridData();
        }
      }
    },

    async copyRolePermission() {
      if (this.add_props.from !=null && this.add_props.to) {
        this.$refs.copy_modal.showLoader(true, "Copying role permission");
        let response = await this.roleStore.copyRole(this.add_props);
        this.$refs.copy_modal.showLoader(false);
        this.msg=response.msg;
        if (response.status) {
          this.$refs.copy_modal.clearForm();
          this.add_props.selected_role=null;
          this.$refs.copy_modal.setMessageOnly(true);
          this.loadGridData();
        }
      }
    },
    async showModal() {
      this.$refs.copy_modal.clearForm();
      this.msg = {};
      this.add_props = {};
      this.isShowCopyModal = true;
    },
    loaderStatusChange(param) {
      this.isShowLoader = param;
    },
  }
}
</script>

<style scoped>

</style>
