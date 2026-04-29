<template>
  <div class="apbd-deal-loader-ctr">
    <div class="apbd-deal-dual-ring">
    </div>
  </div>

</template>

<script>
export default {
  name: "McLoader",
  props: {
    msg: {
      type: String,
      default: 'Loading ...'
    },
  },
}
</script>

<style scoped lang="scss">
.apbd-deal-loader-ctr{
  .apbd-deal-dual-ring {
    display: inline-block;
    width: 16px;
    height: 20px;

  }
  .apbd-deal-dual-ring:after {
    content: " ";
    display: block;
    width: 17px;
    height: 17px;
    margin: 4px;
    border-radius: 50%;
    border: 3px solid var(--apbd-theme-color, rgba(0, 8, 141, 0.33));
    border-color: var(--main-color) transparent var(--apbd-theme-color, rgba(31, 49, 17, 0.1)) transparent;
    animation: lds-dual-ring 1.2s linear infinite;
  }
  @keyframes lds-dual-ring {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }
}


</style>