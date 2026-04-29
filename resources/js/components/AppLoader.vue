<template>
  <div class="loader-ctnr">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"
         style="margin: auto; background: none; display: block; shape-rendering: auto; color:var(--ab-primary,#61cc93);" width="200px" height="200px"
    >
      <circle fill="currentColor" stroke="currentColor" stroke-width="4" r="15" cx="40" cy="100">
        <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1"
                 repeatCount="indefinite" begin="-.4"></animate>
      </circle>
      <circle fill="currentColor" stroke="currentColor" stroke-width="4" r="15" cx="100" cy="100">
        <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1"
                 repeatCount="indefinite" begin="-.2"></animate>
      </circle>
      <circle fill="currentColor" stroke="currentColor" stroke-width="4" r="15" cx="160" cy="100">
        <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1"
                 repeatCount="indefinite" begin="0"></animate>
      </circle>
    </svg>

    <span>{{ this.$translateGettext(msg) }}</span>
  </div>
</template>

<script>
export default {
  name: "AppLoader",
  props: {
    msg: {
      type: String,
      default: 'Loading ...'
    },
    noDropShadow: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    filterDropshadow() {
      return this.noDropShadow ? '' : 'url(#AppLogoDropshadow)';
    }
  }
}
</script>

<style scoped lang="scss">
.loader-ctnr {
  text-align: center;
  padding-bottom: 2rem;
  font-size: 14px;
  font-weight: normal;
}

svg {
  height: 100px;
  perspective: 1rem;

  .ema {
    color: #fff;
  }

  circle.circle-1 {
    stroke: #fff;
  }

  circle.circle-2 {
    stroke: var(--apbd-theme-color, #2563eb);
  }

  text {
    backface-visibility: hidden;
    perspective: 1rem;
    will-change: transform;
    color: #ffffff;
    fill: currentColor;
    text-shadow: 0 0 2px rgba(0, 0, 0, 0.31);
    transform-origin: 50% 50%;
    margin-left: 5px;
  }
}
</style>