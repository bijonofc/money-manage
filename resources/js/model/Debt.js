class Debt {
  constructor() {
    this.id = null;
    this.type = 'owed_to';
    this.creditor_name = '';
    this.creditor_contact = '';
    this.principal_amount = 0;
    this.paid_amount = 0;
    this.account_id = null; // Always null, never ''
    this.due_date = null;
    this.interest_rate = null;
    this.description = '';
    this.status = 'active';
  }
}

export default Debt;
