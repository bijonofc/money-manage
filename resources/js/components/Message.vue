<template>
  <div v-if="msgs?.info || msgs?.error||msgs?.warning||msgs?.debug" class="apbd-msg-container">
    <div v-if="msgs?.info" class="apbd-msg apbd-msg-info" v-for="msg in msgs.info">
      <svg   width="24" height="24" viewBox="0 0 24 24">
        <use href="#apbd-check-circle"/>
      </svg>
      <span v-html="msg"></span>
    </div>
    <div v-if="msgs?.error" class="apbd-msg apbd-msg-danger" v-for="msg in msgs.error">
      <svg   width="24" height="24" viewBox="0 0 24 24">
        <use href="#apbd-times-circle"/>
      </svg>
      <span v-html="msg"></span>
    </div>
    <div v-if="msgs?.warning" class="apbd-msg apbd-msg-warning" v-for="msg in msgs.warning">
      <svg   width="24" height="24" viewBox="0 0 24 24">
        <use href="#apbd-info-circle"/>
      </svg>
      <span v-html="msg"></span>
    </div>
    <div v-if="msgs?.debug" class="apbd-msg apbd-msg-debug" v-for="msg in msgs.debug">
      <svg   width="24" height="24" viewBox="0 0 24 24">
        <use href="#apbd-code"/>
      </svg>
      <span v-html="msg"></span>
    </div>
  </div>
</template>

<script>
export default {
name: "Message",
  props:{
    msgs:{
      default: {}
    }
  },
  computed:{
    errorMsgs(){
      return [];
    }
  },
  methods:{

  }
}
</script>

<style scoped lang="scss">
    .apbd-msg {
      display: flex;
      justify-content: start;
      align-items: center;
      font-size: 12px;
      margin-bottom:10px;

      >svg{
        width:14px;
        margin-right: 5px;
      }
      &.apbd-msg-info{
        background: #32722b;
        color: #fff;
        border-radius: 5px;
        padding: 5px;
        box-shadow: 0 3px 10px -3px #193419;
      }
      &.apbd-msg-danger {
        background: #720000;
        color: #fff;
        border-radius: 5px;
        padding: 5px;
        box-shadow: 0 3px 10px -5px #3b0000;
      }
      &.apbd-msg-warning {
        background: #ffdc1a;
        color: #090909;
        border-radius: 5px;
        padding: 5px;
        box-shadow: 0 3px 10px -5px #4e4b38;
      }
      &.apbd-msg-debug {
        background: #b8f8ff;
        color: #090909;
        border-radius: 5px;
        padding: 5px;
        box-shadow: 0 3px 10px -5px #67999f;
      }
    }
</style>