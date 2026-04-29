<template>
  <div class="loader-content">
    <app-loader :msg="msg" :param="msgParams"  />
  </div>
</template>

<script>
import { AppLoader } from '@appsbd/vue3-appsbd-libs'
export default {
    name: "APBDGridLoader",
    components: { AppLoader },
    props: {
        msg: {
            type: String,
            default: 'Loading ...'
        },
        msgParams: {}
    }
}
</script>

<style scoped lang="scss">

svg{

  circle:nth-child(2){
    stroke: var( --apbd-theme-color,#2563eb) ;
  }
}
</style>
