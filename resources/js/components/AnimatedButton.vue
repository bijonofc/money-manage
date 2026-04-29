<template>
  <button :disabled="disabled" :type="type" class="apbd-animated-btn" @click="clicked($event)" @submit="clicked($event)"  :class="isAnimated?'apbd-animated':''">
   <span :class="{'apbd-ani-text':isHideTextOnAnimate}">
     <slot v-if="!(isAnimated && isHideTextOnAnimate)"></slot>
   </span>

   <span class="icon">
      <slot name="svg">
      <svg class="loader-btn-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" style="height: 1.5em;margin-top: 2px;margin-bottom: -1px;">
        <circle fill="currentColor" stroke="currentColor" stroke-width="15" r="15" cx="40" cy="100">
          <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1"
                   repeatCount="indefinite" begin="-.4"></animate>
        </circle>
        <circle fill="currentColor" stroke="currentColor" stroke-width="15" r="15" cx="100" cy="100">
          <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1"
                   repeatCount="indefinite" begin="-.2"></animate>
        </circle>
        <circle fill="currentColor" stroke="currentColor" stroke-width="15" r="15" cx="160" cy="100">
          <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1"
                   repeatCount="indefinite" begin="0"></animate>
        </circle>
      </svg>
    </slot>
   </span>

  </button>
</template>

<script>
import {useAttrs} from "vue";

export default {
  name: "AnimatedButton",
  emits:['click'],
  props: {
      isHideTextOnAnimate: {
          type: Boolean,
          default: false
      },
      disabled: {
          type: Boolean,
          default: false
      },
      type: {
          type: String,
          default: 'button'
      }
  },
  data(){
    return {
      isAnimated: false
    }
  },
  methods:{
      useAttrs,
    clicked(e){
        console.log(e);
      if(this.isAnimated){
        e.preventDefault();
        return;
      }
      e.showLoader=this.showLoader;
      this.$emit("click",e);
    },
    showLoader(status){
      this.isAnimated=status;
    }
  }
}
</script>

<style scoped lang="scss">

.apbd-animated-btn {
  display: flex;
  align-items: center;
  justify-content: space-between;
  >span.icon {
    display: none;
    align-items: center;
  }

  &.apbd-animated {
    >span.icon {
      margin-left:5px;
      display: flex;
      > svg {

      }
    }
  }
}

.apbd-form-sending{
  .apbd-animated-btn[type=submit] {
    .apbd-ani-text{
      display: none;
    }
    >span.icon {
      margin-left:5px;
      display: flex;
    }
  }
}

</style>
