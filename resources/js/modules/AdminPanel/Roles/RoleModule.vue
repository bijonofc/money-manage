<template>
  <div>
    <div class="card mb-3">
      <div class="card-body p-2">
        <div class="d-flex justify-content-end">
          <ul class="nav apbd-tab-nav w-100">
            <li v-if="true" class="nav-item">
              <router-link to="/admin/roles" class="btn apbd-tab-btn ">
                  <i class="apb apb-shield-alt"></i>
                  <translate>role.list</translate>
              </router-link>
            </li>
            <li v-if="true" class="nav-item">
              <router-link to="/admin/role-access" class="btn apbd-tab-btn">
                  <i class="apb apb-shield-check"></i>
                  <translate>role.access</translate>
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="role-list-panel">
      <router-view></router-view>
    </div>
  </div>


</template>

<script>
import RoleList from "@/modules/AdminPanel/roles/RoleList";
import RoleAccess from "@/modules/AdminPanel/roles/RoleAccess";

export default {
  name: "RoleModule",
  components: {
    RoleAccess,
    RoleList,
  },
  computed: {
  },
  methods: {

  }
}
</script>

<style scoped>

</style>
