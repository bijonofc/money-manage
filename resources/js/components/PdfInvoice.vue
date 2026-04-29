<template>
    <vue-html2pdf
                            ref="html2Pdf"
                            :show-layout="false"
                            :float-layout="true"
                            :enable-download="true"
                            :preview-modal="false"
                            filename="invoice.pdf"
                            :pdf-quality="2"
                            pdf-format="a4"
                            pdf-orientation="portrait"
                        >
                            <section slot="pdf-content">
                               <div v-html="invoiceHtml"></div>
                            </section>

                            <!-- Custom buttons -->
                            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-2 text-center mt-3 mb-3">
                                <button class="btn btn-primary" @click="downloadPdf">
                                    <i class="apb apb-download-01 me-2"></i>
                                    <translate>download.pdf</translate>
                                </button>
                                <button class="btn btn-outline-primary" @click="printInvoice">
                                    <i class="apb apb-printer me-2"></i>
                                    <translate>print.invoice</translate>
                                </button>
                            </div>
                        </vue-html2pdf>

</template>

<script setup>
import { ref, computed,nextTick,onMounted} from 'vue';
import {useRoute} from "vue-router";
const route=useRoute();
const invoiceHtml=ref('');
const html2Pdf = ref();
const props = defineProps({
    invoiceData: {
        type: String,
        default: '',
        required: false
    }
})

const printInvoice = () => {
    const invoice = document.querySelector('.invoice-container');
    if (!invoice) return;

    // Create a hidden iframe
    const iframe = document.createElement('iframe');
    iframe.style.position = 'absolute';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    document.body.appendChild(iframe);

    const doc = iframe.contentWindow.document;

    // Copy all styles
    const styles = Array.from(document.styleSheets)
        .map(sheet => {
            try {
                return Array.from(sheet.cssRules)
                    .map(rule => rule.cssText)
                    .join('');
            } catch (e) {
                return '';
            }
        })
        .join('\n');

    // Build the printable document
    doc.open();
    doc.write(`
        <html lang="bn">
        <head>
            <meta charset="UTF-8">
            <style>
                ${styles}

                .invoice-container, .invoice-container * {
                    box-sizing: border-box;
                }

                @page {
                    size: A4;
                    margin: 10mm;
                }

                body {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                    margin: 0;
                    padding: 0;
                    font-family: Arial, sans-serif;
                }

                img {
                    max-width: 100%;
                    display: block;
                }

                /* Ensure page breaks for long invoices */
                .invoice-container {
                    page-break-inside: avoid;
                }
                .page-break {
                    page-break-after: always;
                }
            </style>
        </head>
        <body>
            ${invoice.outerHTML}
        </body>
        </html>
    `);
    doc.close();

    // Wait for all images to load before printing
    const images = doc.querySelectorAll('img');
    let loaded = 0;

    const printAndRemove = () => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        document.body.removeChild(iframe);
    };

    if (images.length === 0) {
        printAndRemove();
        return;
    }

    images.forEach(img => {
        if (img.complete) {
            loaded++;
            if (loaded === images.length) printAndRemove();
        } else {
            img.onload = img.onerror = () => {
                loaded++;
                if (loaded === images.length) printAndRemove();
            };
        }
    });
};

onMounted(() => {
    invoiceHtml.value = history.state?.html || props.invoiceData || "<h3>No invoice data found</h3>";
});
// Custom download
const downloadPdf = async () => {
    try {
        await nextTick();

        if (html2Pdf.value && typeof html2Pdf.value.generatePdf === 'function') {
            html2Pdf.value.generatePdf();
        } else {
            console.error('html2Pdf component not properly initialized');

            fallbackDownload();
        }
    } catch (error) {
        console.error('Error generating PDF:', error);
        fallbackDownload();
    }
};
const pdfFileName = computed(() => {
   return route.params.id;
});
// Alternative download method
const fallbackDownload = () => {

    const element = document.querySelector('.invoice-container');
    const opt = {
        margin: 1,
        filename: pdfFileName.value,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    import('html2pdf.js').then((module) => {
        const html2pdf = module.default;
        html2pdf().from(element).set(opt).save();
    });
};
</script>

<style scoped lang="scss">
.reg-content {
    overflow: auto;
    height: calc(100dvh - 80px);
}

@media (max-width: 768px) {
    .reg-content {
        height: calc(100dvh - 180px);
    }
}

</style>
