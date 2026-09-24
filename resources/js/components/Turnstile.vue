<template>
    <div v-if="siteKey" class="turnstile-wrapper">
        <div class="turnstile" ref="turnstileEl" data-size="flexible"></div>
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
            default: false
        },
        transReload: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            widgetId: null,
            pollTimer: null,
        };
    },
    mounted() {
        this.initTurnstile();
    },
    beforeUnmount() {
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
        }
        if (this.widgetId !== null && window.turnstile) {
            try {
                window.turnstile.remove(this.widgetId);
            } catch (e) {}
        }
    },
    methods: {
        initTurnstile() {
            if (window.turnstile) {
                this.Reload();
            } else {
                let attempts = 0;
                this.pollTimer = setInterval(() => {
                    attempts++;
                    if (window.turnstile) {
                        clearInterval(this.pollTimer);
                        this.pollTimer = null;
                        this.Reload();
                    } else if (attempts > 50) {
                        clearInterval(this.pollTimer);
                        this.pollTimer = null;
                    }
                }, 100);
            }
        },
        Reload() {
            if (window.turnstile && this.siteKey && this.$refs.turnstileEl) {
                try {
                    if (this.widgetId !== null && this.widgetId !== undefined) {
                        window.turnstile.reset(this.widgetId);
                    } else {
                        this.widgetId = window.turnstile.render(this.$refs.turnstileEl, {
                            sitekey: this.siteKey,
                            callback: (token) => this.$emit("verified", token),
                            size: "flexible",
                        });
                    }
                } catch (e) {
                    console.error("Turnstile render error:", e);
                }
            }
        },
        onVerify(token) {
            this.$emit("verified", token);
        },
    },
}
</script>

<style scoped lang="scss">
.turnstile-wrapper {
    width: 100%;
    min-height: 65px;
    margin: 0;
    padding: 0;
}

.turnstile {
    width: 100%;
}

:deep(.turnstile),
:deep(.turnstile > div),
:deep(.turnstile iframe) {
    width: 100% !important;
    max-width: 100% !important;
}
</style>
