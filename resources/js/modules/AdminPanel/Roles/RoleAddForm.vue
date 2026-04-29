<template>
<div class="row">
    <div class="col-sm">
      <div class="mb-2">
        <label for="title" v-translate>gbl.title</label>
        <Field label="gbl.title" type="text" v-model="formProps.title" rules="required" name="title"
               id="title" class="form-control form-control-sm form-control-md"/>
        <ErrorMessage name="title" class="apbd-v-error"/>
      </div>
    </div>
</div>
  <div class="row">
  <div class="form-row">
    <div class="col-sm">
      <div class=" mb-2">
          <label for="description" v-translate>gbl.description</label>
          <Field as="textarea" label="gbl.description" type="text" v-model="formProps.description" rules="" name="description"
                 id="description" class="form-control form-control-sm form-control-md"/>
          <ErrorMessage name="description" class="apbd-v-error"/>
       </div>
    </div>
  </div>
</div>
  <div class="row">
    <div class="col-sm">
      <div class="d-flex justify-content-start align-items-center">
        <label for="is_super" class="me-3" v-translate>is.super</label>
        <div class="form-check form-switch form-switch-sm mt-0">
          <input class="form-check-input" true-value="Y" false-value="N" v-model="formProps.is_super" type="checkbox" id="is_super" name="status">
        </div>
      </div>
    </div>
    <div class="col-sm">
      <div class="d-flex justify-content-end align-items-center">
        <label for="status" class="me-3" v-translate>gbl.status</label>
        <div class="form-check form-switch form-switch-sm mt-0">
          <input class="form-check-input" true-value="A" false-value="I" v-model="formProps.status" type="checkbox" id="status" name="status">
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import { Field,ErrorMessage  } from "vee-validate";
export default {
  name: "RoleAddForm",
  components: {
    Field, ErrorMessage
  },
  props: {
    formProps: {
      type: Object,
      default: {}
    }
  },
}
</script>

<style scoped>

</style>
