<template>
    <div v-if="this.siteKey" class="turnstile-wrapper mb-2">
        <div class="turnstile" ref="turnstileEl" ></div>
    </div>
</template>


<script>
export default {
    name: "Turnstile",
    props: {
        siteKey: {
            type: String,
            required: true,
        },
        isHidden: {
            type: Boolean,
            default:false
        },
        transReload: {
            type: Boolean,
            default:false
        },
    },
    mounted() {
       this.Reload();
    },

    methods: {
        Reload(){
            if (window.turnstile) {
                if (this.siteKey)
                {
                    window.turnstile.render(this.$refs.turnstileEl, {
                        sitekey: this.siteKey,
                        callback: (token) => this.$emit("verified", token),
                        size: "flexible",
                    });
                }
            }
        },
        onVerify(token) {
            // emit event to parent
            console.log(token);
            this.$emit("verified", token);
        },
    },
}
</script>

<style scoped lang="scss">

</style>
