<template>
    <div class="d-flex align-items-center justify-content-between header-brand flex-wrap">

        <div class="brand-subtitle">

            <a href="https://appsbd.com/" target="_blank" class="company-logo">
                <img src="../assets/appsbd_black.png" alt="Company Logo" />
            </a>
            <span v-translate>gbl.product.of</span>
        </div>
        <div class="brand-title">
            <a href="https://nogorpos.com/" target="_blank" class="brand-link">
                <img
                    src="../assets/black.png"
                    alt="NogorPOS Logo"
                    class="brand-icon"
                />
                <span class="brand-text">POS</span>
            </a>
        </div>
    </div>
</template>

<script setup>
</script>

<style scoped lang="scss">
[data-bs-theme="dark"] .company-logo {
    filter: invert(100%) brightness(1000%);
}
[data-bs-theme="dark"] .brand-icon {
    filter: invert(100%) brightness(1000%);
}
.header-brand {
    //padding: 0.8rem 1rem;

    flex-wrap: wrap;

    .brand-title {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;

        .brand-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;

            .brand-icon {
                height: 45px;
                width: auto;
                transition: transform 0.3s ease;
            }

            .brand-text {
                font-size: 1.2rem;
                font-weight: 700;
                color: var(--ab-body-color);
                letter-spacing: 1px;
                margin-top: 16px;

                &:hover {
                    color: #F2B926;
                }
            }

            &:hover .brand-icon {
                transform: scale(1.08);
            }
        }
    }

    .brand-subtitle {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        color: var(--ab-body-color);

        a.company-logo {
           // margin-left: 4px;
            display: inline-flex;

            img {
                height: 40px;
                width: auto;
                border-radius: 6px;
                transition: transform 0.3s ease;

                &:hover {
                    transform: scale(1.05);
                }
            }
        }
    }

    @media (max-width: 576px) {

        .brand-title {
            margin-bottom: 0.2rem;

            .brand-link {
                gap:0.3rem;
                .brand-icon {
                    height: 30px;
                }
                .brand-text {
                    font-size: 0.7rem;
                    margin-top: 9px;
                }
            }
        }

        .brand-subtitle {
            font-size: 0.7rem;

            a.company-logo img {
                height: 30px;
            }
        }
    }
}
</style>
