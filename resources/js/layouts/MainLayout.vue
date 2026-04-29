<template>
    <Headermenu :title="title"/>
    <div class="container-fluid ">

        <div class="row">

           <Sidemenu/>
            <!-- Main Content -->

            <div class="col-md pt-3">
                <router-view></router-view>
            </div>

        </div>
    </div>
</template>

<script setup>
import Headermenu from '@/components/headermenu.vue';
import Sidemenu from '@/components/sidemenu.vue';




defineProps({
    title: {
        type: String,
        required: true
    }
})


</script>
