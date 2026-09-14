class Budget {
  constructor() {
    this.id = null;
    this.category_id = null; // Always null, never ''
    this.amount = 0;
    this.period = 'monthly';
    this.start_date = new Date().toISOString().split('T')[0];
    this.end_date = null;
    this.alert_threshold = 80;
    this.is_active = true;
  }
}

export default Budget;
