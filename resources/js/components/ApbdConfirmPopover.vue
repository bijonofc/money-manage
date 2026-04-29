<template>

  <VDropdown placement="top-start">
    <slot>
      <button type="button" class="btn" :class="buttonClass">
        <i v-if="iconClass" class="apb" :class="iconClass"></i>
        <span v-if="buttonText" v-translate>{{ buttonText }}</span>
      </button>
    </slot>
    <template #popper>
      <div class="p-3" :class="isLoading?'apbd-loading-parent':''">
        <div class="text-center">{{ msg }}</div>
        <Message v-if="!isLoading" :msgs="msgs" class="mb-1 mt-1"/>
        <slot name="desc"></slot>
        <div class="d-flex justify-content-center align-items-center mt-2 gap-2">
          <slot name="actionButtons" :removeConfirmed="removeConfirmed">

            <button ref="remove" class="btn btn-sm btn-danger apbd-loading-btn " @click="removeConfirmed()">
              <translate class="apbd-loading-hide">gbl.yes</translate>
            </button>
            <button v-close-popper.all class="btn btn-sm btn-success apbd-loading-hide" v-translate>gbl.no</button>
          </slot>
        </div>
      </div>
    </template>
  </VDropdown>


</template>

<script>
import {hideAllPoppers} from 'floating-vue'

import Message from "@/components/Message.vue";

export default {
  name: "ApbdConfirmPopover",
  components: {Message},
  props: {
    msg: {
      default: 'Are you sure?'
    },
      position:{
        default:''
      },
    itemData: {
      default: null
    },
    buttonText: {
      default: null,
    },
    buttonClass: {
      default: null,
    },
    iconClass: {
      default: null,
    }
  },
  data() {
    return {
      isLoading: false,
      msgs: null,
    }
  },
  methods: {
    closePopover() {
      hideAllPoppers();
    },
    showLoader(status) {
      this.isLoading = status;
    },
    showMsg(msg) {
      this.msgs = msg;
    },
    removeConfirmed() {
      this.$emit("onConfirmed", {
        showLoader: this.showLoader,
        showMsg: this.showMsg,
        itemData: this.itemData,
        closePopover: this.closePopover
      });
    }
  }
}
</script>
<style lang="scss">

.v-popper--theme-dropdown .v-popper__inner {
  background: var(--vtu-card-bg, #FFFFFF);
  color: var(--ab-body-color, #000);
  border-radius: 5px;
  border: 1px solid var(--ab-border-color);
}

.v-popper--theme-dropdown .v-popper__arrow-outer {
   // display: none;
  border-color: var(--ab-border-color);
}

.v-popper--theme-dropdown .v-popper__arrow-inner {
   // display: none;
    border-color: var(--ab-card-bg );
}


</style>

<style scoped lang="scss">


.apbd-loading-parent {

  .apbd-loading-hide {
    display: none;
  }

  .apbd-loading-btn {
    pointer-events: none;
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: nowrap;
    padding-right: 30px;

    .apbd-loading-hide {
      visibility: hidden;
      width: 0;
      white-space: nowrap;
      overflow: hidden;
      display: inline-block;
    }

    &::after {
      display: inline-block;
      position: absolute;
      right: 5px;

      height: 100%;
      background-position: center;
      content: " ";
      width: 26px;
      min-height: 26px;
      background-size: cover;
      margin-left: 15px;
      margin-top: 2px;
      background-repeat: no-repeat;
      background-image: url("data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8'%3F%3E%3Csvg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='margin: auto; background: transparent none repeat scroll 0%25 0%25; display: block; shape-rendering: auto;' width='200px' height='200px' viewBox='0 0 100 100' preserveAspectRatio='xMidYMid'%3E%3Ccircle cx='84' cy='50' r='10' fill='%23fdfdfd'%3E%3Canimate attributeName='r' repeatCount='indefinite' dur='0.25s' calcMode='spline' keyTimes='0;1' values='10;0' keySplines='0 0.5 0.5 1' begin='0s'%3E%3C/animate%3E%3Canimate attributeName='fill' repeatCount='indefinite' dur='1s' calcMode='discrete' keyTimes='0;0.25;0.5;0.75;1' values='%23fdfdfd;%23fdfdfd;%23fdfdfd;%23fdfdfd;%23fdfdfd' begin='0s'%3E%3C/animate%3E%3C/circle%3E%3Ccircle cx='16' cy='50' r='10' fill='%23fdfdfd'%3E%3Canimate attributeName='r' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='0;0;10;10;10' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='0s'%3E%3C/animate%3E%3Canimate attributeName='cx' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='16;16;16;50;84' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='0s'%3E%3C/animate%3E%3C/circle%3E%3Ccircle cx='50' cy='50' r='10' fill='%23fdfdfd'%3E%3Canimate attributeName='r' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='0;0;10;10;10' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='-0.25s'%3E%3C/animate%3E%3Canimate attributeName='cx' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='16;16;16;50;84' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='-0.25s'%3E%3C/animate%3E%3C/circle%3E%3Ccircle cx='84' cy='50' r='10' fill='%23fdfdfd'%3E%3Canimate attributeName='r' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='0;0;10;10;10' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='-0.5s'%3E%3C/animate%3E%3Canimate attributeName='cx' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='16;16;16;50;84' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='-0.5s'%3E%3C/animate%3E%3C/circle%3E%3Ccircle cx='16' cy='50' r='10' fill='%23fdfdfd'%3E%3Canimate attributeName='r' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='0;0;10;10;10' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='-0.75s'%3E%3C/animate%3E%3Canimate attributeName='cx' repeatCount='indefinite' dur='1s' calcMode='spline' keyTimes='0;0.25;0.5;0.75;1' values='16;16;16;50;84' keySplines='0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1' begin='-0.75s'%3E%3C/animate%3E%3C/circle%3E%3C/svg%3E");
    }
  }
}




</style>
