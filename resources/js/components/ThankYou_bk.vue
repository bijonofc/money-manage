<template>
    <div v-if="true" class="success-wrapper text-center py-3">
                  <span class="success-icon">
                      <i class="apb apb-circle-check"></i>
                  </span>
        <h5 v-translate>thank.msg</h5>
        <p  class="text-muted mt-2">
            {{ msg }}
        </p>
        <a href="https://nogorpos.com/" target="_blank" class="btn btn-success">
            <i class="apb apb-Home-01 me-2"></i>
            <translate>go.home</translate>
        </a>

    </div>
</template>
<script setup>
const props=defineProps({

    isShowLoader:{type:Boolean,default:false},
    msg:{type:String,default:'আমাদের সাপোর্ট টিম শীঘ্রই আপনার সাথে যোগাযোগ করবে।'}
})
</script>

<style scoped lang="scss">
.success-wrapper {
    background: var(--ab-card-bg);
    border: 1px solid var(--ab-border-color);
    border-radius: 12px;
    box-shadow: 0 2px 8px #4fc09873;
    max-width: 546px;
    margin: 4rem auto;
    padding: 1rem;
    animation: fadeIn-37f0ea8f 0.5s ease-in-out;

    .success-icon {
        font-size: 3rem;
        color:#179545;
    }
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.btn-success {
    background-color: #47dc69;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.2s ease;

    &:hover {
        background-color: #72bb8b;
        transform: scale(1.03);
    }
}
</style>
