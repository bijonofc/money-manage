class Transaction {
  constructor() {
    this.id = null;
    this.transaction_type = 'expense';
    this.amount = 0;
    this.account_id = null; // Always null, never ''
    this.category_id = null; // Always null, never ''
    this.from_account_id = null; // Always null, never ''
    this.date = new Date().toISOString().split('T')[0];
    this.time = '';
    this.description = '';
    this.reference_number = '';
    this.payment_method = 'cash';
    this.tags = [];
  }
}

export default Transaction;
