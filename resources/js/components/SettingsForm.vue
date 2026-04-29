<template>
  <Form ref="main_form" @reset="clearForm" :class="is_sending?'apbd-form-sending':''" @submit="onFormSubmit" class="needs-validation">
    <slot/>
  </Form>
</template>

<script>
import {Form} from "vee-validate";

export default {
  name: "SettingsForm",
  props:{
    onSubmit: {
      type: Function,
      default: ()=>{}
    },
  },
  data(){
    return{
      is_sending:false,
    }
  },
  components: {
    Form,
  },
  methods: {
    async onFormSubmit(data) {
      this.is_sending=true;
      if (this.onSubmit && typeof this.onSubmit === 'function') {
        await this.onSubmit(data);
      }
      this.is_sending=false;
    },
    clearForm(){
      try {
        this.$refs.main_form.resetForm();
      } catch (e) {
        console.log(e.message);
      }
    },
  }
}
</script>
<style lang="scss">
@keyframes gradient-animation {
  0%{
    background-position: 400% 0
  }
  100%{
    background-position: 0 0
  }
}

.apbd-form-sending {
  button[type=submit] {
    pointer-events: none;
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-right: 60px !important;
    margin: 0 auto;
    &.btn-theme {
      color: var(--ab-btn-color, #fff) !important;
      background-color: var(--ab-btn-bg-disable-color) !important;
      border-color: var(--ab-btn-bg-color) !important;
      opacity: 1 !important;
    }

    &::after {
      display: block;
      height: 100%;
      background-position: center;
      position:absolute;
      right: 5px;
      content: " ";
      width: 2.5rem;
      background-size: cover;
      margin-left: 15px;
      margin-top: 2px;
      background-repeat: no-repeat;
      background-image: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8'%3F%3E%3Csvg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='margin: auto; background: transparent none repeat scroll 0%25 0%25; display: block; shape-rendering: auto;' width='200px' height='200px' viewBox='0 0 100 100' preserveAspectRatio='xMidYMid'%3E%3Ccircle cx='84' cy='50' r='10' fill='%23fdfdfd'%3E%3Canimate attributeName='r' repeatCount='indefinite' dur='0.25s' calcMode='spline' keyTimes='0;1' values='10;0' keySplines='0 0.5 0.5 1' begin='0s'%3E%3C/animate%3E%3Canimate attributeName='fill' repeatCount='indefinite' dur='1s' calcMode='discrete' keyTimes='0;0.25;0.5;0.75;1' values='%23fdfdfd;%23fdfdfd;%23fdfdfd;%23fdfdfd;%23fdfdfd' begin='0s'%3E%3C/animate%3E%3C/circle%3E%3Ccircle cx='16' cy='50' r='10' fill='%23fdfdfd'%3E%3Canimate attributeName='r' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='0;0;10;10;10' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='0s'%3E%3C/animate%3E%3Canimate attributeName='cx' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='16;16;16;50;84' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='0s'%3E%3C/animate%3E%3C/circle%3E%3Ccircle cx='50' cy='50' r='10' fill='%23fdfdfd'%3E%3Canimate attributeName='r' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='0;0;10;10;10' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='-0.25s'%3E%3C/animate%3E%3Canimate attributeName='cx' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='16;16;16;50;84' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='-0.25s'%3E%3C/animate%3E%3C/circle%3E%3Ccircle cx='84' cy='50' r='10' fill='%23fdfdfd'%3E%3Canimate attributeName='r' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='0;0;10;10;10' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='-0.5s'%3E%3C/animate%3E%3Canimate attributeName='cx' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='16;16;16;50;84' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='-0.5s'%3E%3C/animate%3E%3C/circle%3E%3Ccircle cx='16' cy='50' r='10' fill='%23fdfdfd'%3E%3Canimate attributeName='r' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='0;0;10;10;10' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='-0.75s'%3E%3C/animate%3E%3Canimate attributeName='cx' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='16;16;16;50;84' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='-0.75s'%3E%3C/animate%3E%3C/circle%3E%3C/svg%3E");

    }
  }

  .apbd-loading-target {
    position: relative;

    &:after {
      display: block;
      content: " ";
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
      background: #FFF;
      background: linear-gradient(to right, #F6F6F6 8%, #F0F0F0 18%, #F6F6F6 33%);
      background-size: 400% 400%;
      animation: gradient-animation 5s linear infinite;
      opacity: 0.5;
    }
  }
}
</style>
