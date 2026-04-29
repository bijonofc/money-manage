<template>
    <div class="login-container">
        <div class="login-box">
            <forget-password-fields
                v-model:email="email"
                :loading="loading"
                :message="msgs"
                :back-route="`/${adminLoginKey}`"
                @submit="handleSubmit"
            />
            <Turnstile ref="turnstile" :site-key="rootData.site_key" @verified="verifiedToken" />
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref,inject } from 'vue'
import { useRouter } from 'vue-router'
import { useLoginStore } from './loginStore'
import ForgetPasswordFields from "@/components/ForgetPasswordFields.vue";
import Turnstile from "@/components/Turnstile.vue";
const adminLoginKey = import.meta.env.VITE_ADMIN_LOGIN_KEY || "upslogin";
const email = ref('');
const msgs = ref({});
const loading = ref(false);
const store = useLoginStore()
const router = useRouter()
const rootData = inject('rootData');

const turnstile=ref(null);
const transReloaded=ref(false);
const transToken=ref('');
const verifiedToken = async (token) => {
    transReloaded.value=true;
    transToken.value=token;
};
onMounted(() => {
    if (import.meta.env.MODE === 'development') {
        email.value = 'test@example.com'
    }
    if (store.isLoggedIn) {
        router.push('/')
    }
})
const handleSubmit = async () => {
  try {
      loading.value=true;
      let res = await store.forget_password({email:email.value,token:transToken.value});
      msgs.value = res.msg;
      transReloaded.value=false
      turnstile.value?.Reload();
      if (res.status)
      {
       router.push({ name: 'login' });
      }
  }catch (e) {
      console.log(e);
  }
  loading.value=false;


}

</script>

<style scoped>
body {
    background-color: #f4f6fa;
}

.login-box {
    max-width: 400px;
    margin: 80px auto;
    padding: 30px;
    background: var(--ab-card-bg);
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.logo {
    font-weight: bold;
    font-size: 28px;
    color: #0d6efd;
}

.divider {
    text-align: center;
    margin: 20px 0;
}

.divider:before,
.divider:after {
    content: '';
    display: inline-block;
    width: 40%;
    height: 1px;
    background: #ddd;
    vertical-align: middle;
}

.divider:before {
    margin-right: 10px;
}

.divider:after {
    margin-left: 10px;
}
</style>
