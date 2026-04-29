<template>
    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="row">
                <div class="col-md-6">
                    <div class="company-logo">
                        <i class="bi bi-building"></i> Appsbd
                    </div>
                    <p class="mb-0 mt-2">Mofiz Paglar Mor, Sherpur Road (West)</p>
                    <p class="mb-0">Bogura Sadar, Bogura</p>
                    <p class="mb-0">+8809696025530</p>
                    <p class="mb-0">support@appsbd.com</p>
                </div>
                <div class="col-md-6 text-end">
                    <h1 class="invoice-title">INVOICE</h1>
                    <div class="mt-3">
                        <span class="status-badge text-white" :class="statusInfo.class">
                            {{ statusInfo.text }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="text-primary">Bill To:</h5>
                <p class="mb-0 fw-bold">{{ customer.name }}</p>
                <p class="mb-0">{{ customer.address }}</p>
                <p class="mb-0">{{ customer.email }}</p>
                <p class="mb-0">{{ customer.phone }}</p>
            </div>
            <div class="col-md-6 text-end">
                <table class="table table-borderless">
                    <tbody>
                    <tr>
                        <td class="fw-bold text-end pe-3">Invoice #:</td>
                        <td class="text-start">{{ invoice.prefix }}{{ invoice.invoice_number }}{{ invoice.suffix }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end pe-3">Invoice Date:</td>
                        <td class="text-start">{{ appHelper.formatDateTime(invoice.invoice_date) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end pe-3">Due Date:</td>
                        <td class="text-start">{{ appHelper.formatDateTime(invoice.due_date) }}</td>
                    </tr>
                    <tr v-if="invoice.paid_at">
                        <td class="fw-bold text-end pe-3">Paid Date:</td>
                        <td class="text-start">{{ appHelper.formatDateTime(invoice.paid_at) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="table-responsive mb-4">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in invoice.items" :key="item.id">
                    <td>
                        <div class="fw-bold">{{ item.name }}</div>
                        <div class="text-muted small" v-if="item.description">{{ item.description }}</div>
                    </td>
                    <td class="text-center">{{ item.quantity }}</td>
                    <td class="text-end">{{ formatCurrency(item.unit_price) }}</td>
                    <td class="text-end">{{ formatCurrency(item.quantity * item.unit_price) }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Invoice Summary -->
        <div class="row">
            <div class="col-md-6">
                <div class="notes-section" v-if="invoice.notes">
                    <h6 class="text-primary">Notes</h6>
                    <p class="mb-0">{{ invoice.notes }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="summary-card p-3">
                    <table class="table table-borderless">
                        <tbody>
                        <tr>
                            <td class="fw-bold">Subtotal:</td>
                            <td class="text-end">{{ formatCurrency(invoice.subtotal) }}</td>
                        </tr>
                        <tr v-if="invoice.discount_amount > 0">
                            <td class="fw-bold">
                                Discount
                                <span v-if="invoice.discount_type === 'P'">({{ invoice.discount_amount }}%)</span>:
                            </td>
                            <td class="text-end text-danger">-{{ formatCurrency(calculateDiscount()) }}</td>
                        </tr>
                        <tr v-if="invoice.tax_amount > 0">
                            <td class="fw-bold">Tax ({{ invoice.tax_percentage }}%):</td>
                            <td class="text-end">{{ formatCurrency(invoice.tax_amount) }}</td>
                        </tr>
                        <tr v-if="invoice.free_month > 0">
                            <td class="fw-bold">Free Month:</td>
                            <td class="text-end">{{ invoice.free_month }} month(s)</td>
                        </tr>
                        <tr class="border-top">
                            <td class="fw-bold fs-5">Total:</td>
                            <td class="text-end fs-5 fw-bold">{{ formatCurrency(invoice.total_amount) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="row mt-5 pt-3 border-top">
            <div class="col-md-6">
                <p class="text-muted small">Thank you for your business!</p>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary me-2" @click="printInvoice">
                    <i class="apb apb-printer-06"></i> Print Invoice
                </button>
                <button class="btn btn-outline-primary">
                    <i class="apb apb-download-04"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, computed } from 'vue';
import appHelper from "../libs/AppHelper.js";
const invoice = ref({
    id: 1,
    invoice_number: '001',
    ref_id: 'REF-123',
    prefix: 'INV-',
    suffix: 'NPS',
    customer_id: 1,
    created_by: 1,
    subtotal: 450.00,
    discount_amount: 10,
    discount_type: 'P', // P=Percentage, F=Fixed
    tax_amount: 40.50,
    tax_percentage: 9,
    total_amount: 445.50,
    coupon_code: 'SAVE10',
    free_month: 3,
    currency: 'BDT',
    status: 'C', // P=Pending, C=Completed,F=Failed,X=Cancelled
    notes: 'Thank you for your prompt payment. We appreciate your business.',
    invoice_date: '2023-04-15',
    due_date: '2023-05-15',
    paid_at: '2023-04-20',
    created_at: '2023-04-15T10:30:00Z',
    updated_at: '2023-04-20T14:45:00Z',
    items: [
        {
            id: 1,
            name: 'Starter',
            description: 'Homepage and contact form',
            quantity: 1,
            unit_price: 300.00,
            discount_amount: 10,
            vat: 40.50,
            vat_percentage: 9,
            total: 445.50,

        },
        {
            id: 2,
            name: 'Enterprise',
            description: 'Homepage and contact form',
            quantity: 1,
            unit_price: 700.00,
            discount_amount: 50,
            vat: 50.50,
            vat_percentage: 5,
            total: 754.50,
        }
    ]
});

// Sample customer data
const customer = ref({
    id: 1,
    name: 'John Smith',
    email: 'john.smith@example.com',
    phone: '(555) 123-4567',
    address: '123 Customer Street, Client City, CC 12345'
});
const statusMap = {
    p:  { text: 'Pending',    class: 'bg-warning' },
    c:  { text: 'Completed',  class: 'bg-success' },
    f:  { text: 'Failed',     class: 'bg-danger' },
    x:  { text: 'Cancelled',  class: 'bg-secondary' },
};

const statusInfo = computed(() => {
    const key = invoice.value.status?.toLowerCase() ?? 'p';
    return statusMap[key] || { text: 'Unknown', class: 'bg-secondary' };
});
// Format currency based on invoice currency
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: invoice.value.currency
    }).format(amount);
};

// Format date

// Calculate discount amount
const calculateDiscount = () => {
    if (invoice.value.discount_type === 'percentage') {
        return (invoice.value.subtotal * invoice.value.discount_amount) / 100;
    }
    return invoice.value.discount_amount;
};

// Print invoice function
const printInvoice = () => {
    window.print();
};
</script>
<style scoped lang="scss">
.invoice-container {
    max-width: 850px;
    margin: 30px auto;
    padding: 30px;
    background: white;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}
.invoice-header {
    border-bottom: 2px solid #4e73df;
    padding-bottom: 20px;
    margin-bottom: 30px;
}
.company-logo {
    font-size: 24px;
    font-weight: bold;
    color: #4e73df;
}
.invoice-title {
    font-size: 28px;
    font-weight: 700;
    color: #2e59d9;
}
.status-badge {
    font-size: 14px;
    padding: 6px 12px;
    border-radius: 20px;
}
.table thead th {
    background-color: #4e73df;
    color: white;
    border: none;
}
.summary-card {
    background-color: #f8f9fc;
    border-radius: 8px;
    border-left: 4px solid #4e73df;
}
.text-primary {
    color: #4e73df !important;
}
.bg-primary {
    background-color: #4e73df !important;
}
.btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
}
.notes-section {
    background-color: #f8f9fc;
    border-radius: 8px;
    padding: 15px;
}
</style>
