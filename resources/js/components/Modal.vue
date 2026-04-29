<template>
    <!-- Modal -->
    <div class="modal show fade ab-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel">
      <div :class="[modalSize,modalScreen,isModalCenter?' modal-dialog-centered':'',]" class="modal-dialog">
        <div class="modal-content">
         
          <div class="modal-header" v-if="!hideHeader">
            <slot name="header">
              This is  default modal header title!
             </slot>
            <button v-if="!isHideHeaderBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="close"></button>
          </div>
          <Form :as="hideForm?'div':'form'" ref="modal_form"  @submit="onSubmit" @reset="clearForm"  class="needs-validation" >
            <div  class="modal-body" :class="bodyClass">
              <response-msg :disable-remove="true" :message="modalMsg"/>
              <slot v-if="!isHideFooter" name="body"  >
                This is the default body!
              </slot>
  
              <div class="modal-loader" v-show="isShowLoader">
                <div class="loader-content">
                  <slot name="loader">
                   <AppLoader :no-drop-shadow="true" :msg="loading_msg"/>
                  </slot>
                </div>
              </div>
            </div>
            <!-- <div class="modal-body" :class="bodyClass">
            <response-msg :disable-remove="true" :message="modalMsg" />
              <slot  name="body">
                This is the default body!
              </slot>
              <div class="modal-loader" v-show="isShowLoader">
                <div class="loader-content">
                  <slot name="loader">
                   <AppLoader  />
                  </slot>
                </div>
              </div>
            </div> -->
            <div v-if="!hideFooter && !isHideFooter && !isShowLoader" class="modal-footer">
              <slot name="footer" :close="close">
                This is the default footer!
              </slot>
            </div>
          <div v-if="!hideFooter && ( isHideFooter || isShowLoader)" class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="close" v-translate>Close</button>
          </div>
          </Form >
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import {Form} from "vee-validate";
  import AppLoader from "@/components/AppLoader";
  import ResponseMsg from "@/components/ResponseMsg";
  export default {
    name: 'Modal',
    props: {
      isModalVisible: Boolean,
      modalSize:String,
      modalScreen:{
        type:String,
        default:''
      },
      modalMsg:{
        type:String,
        default:""
      },
      hideHeader:{
        type:Boolean,
        default:false
      },
      hideFooter:{
        type:Boolean,
        default:false
      },
      hideCrossBtn:{
        type:Boolean,
        default:false
      },
      hideForm:{
        type:Boolean,     
        default:false   
      },
      bodyClass:{
        type:String,
        default:""
      },
      isModalCenter:{
        type:Boolean,
        default:true
      }
    },
    components: {
      ResponseMsg,
      AppLoader,
      Form
    },
    data() {
      return {
        isShowLoaderProp:false,
        modalLoadingMsg:'',
        modalMsgOnly:{},
        isHideFooter:false,
        isHideHeaderBtn:false,
        initialValues:{},
      }
    },
    created() {
    
    },
    mounted() {
    },
    computed:{
      isShowLoader(){
        return this.isShowLoaderProp?this.isShowLoaderProp:false;
      },
      loading_msg(){
        return this.modalLoadingMsg;
      },
      isHideBtn(){
        try {
          return this.hideCrossBtn;
        }catch (e) {
          console.log(e.message);
        }
      }
    },
    methods: {
      onSubmit($event, {resetForm}) {
        this.$emit('onSubmit', {$event, resetForm});
      },
      showLoader(status,msg){
        this.isShowLoaderProp=status;
        this.$emit('loading-status',!this.isShowLoaderProp);
        if(msg) {
          this.modalLoadingMsg = msg;
        }
      },
      close() {
        this.clearForm();
        this.$emit('close');
      },
      clearForm(){
        this.modalMsgOnly= {};
        this.isHideFooter=false;
        try {
          this.$refs.modal_form.setValues({});
          this.$refs.modal_form.resetForm();
        } catch (e) {
          console.log(e.message);
        }
        //this.$refs.modal_form.setValues({});
        //this.$refs.modal_form.resetForm();
      },
      returnClear(){
        this.modalMsgOnly= {};
        this.isHideFooter=false;
        this.$refs.modal_form.resetForm();
      },
      showMsgOnly(msg,isHideFooter){
        this.modalMsgOnly=msg;
        this.isHideFooter=isHideFooter;
      },
      setMessageOnly(status){
        this.isHideFooter=status;
      }
  
    }
  }
  </script>
  
  
  <style scoped lang="scss">
  .modal{
    &.show {
      display: block;
      background: rgba(0, 0, 0, 0.47);
    }
  }
  
  </style>
  